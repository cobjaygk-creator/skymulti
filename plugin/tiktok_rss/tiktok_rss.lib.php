<?php
if (!defined('_GNUBOARD_')) exit;

function skymulti_tiktok_log($message) {
    $dir = G5_DATA_PATH.'/tiktok_rss';
    if (!is_dir($dir)) { @mkdir($dir, G5_DIR_PERMISSION, true); @chmod($dir, G5_DIR_PERMISSION); }
    @file_put_contents($dir.'/sync.log', '['.date('Y-m-d H:i:s').'] '.$message.PHP_EOL, FILE_APPEND | LOCK_EX);
}

function skymulti_tiktok_video_id($value) {
    if (preg_match('~(?:video/|embed/v2/|player/v1/)([0-9]{8,})~i', (string)$value, $m)) return $m[1];
    if (preg_match('/^([0-9]{8,})$/', trim((string)$value), $m)) return $m[1];
    return '';
}

function skymulti_tiktok_download($url, $timeout, $referer = '') {
    if (!$url || !function_exists('curl_init')) throw new Exception('cURL is unavailable.');
    $last_code = 0;
    $last_error = '';
    for ($attempt = 1; $attempt <= 3; $attempt++) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120 Safari/537.36');
        $accept = $referer ? 'Accept: image/avif,image/webp,image/apng,image/*,*/*;q=0.8' : 'Accept: application/rss+xml,application/xml,text/xml,*/*;q=0.8';
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($accept, 'Accept-Language: ko-KR,ko;q=0.9,en;q=0.8'));
        if ($referer) curl_setopt($ch, CURLOPT_REFERER, $referer);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, (int)$timeout);
        $body = curl_exec($ch);
        $last_code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $last_error = curl_error($ch);
        curl_close($ch);
        if ($body !== false && $last_code >= 200 && $last_code < 300) return $body;
        if ($attempt < 3) usleep($attempt * 500000);
    }
    throw new Exception('Download failed after 3 attempts (HTTP '.$last_code.'): '.$last_error);
}

function skymulti_tiktok_save_thumbnail($bo_table, $write_table, $wr_id, $video_id, $url) {
    global $g5;
    if (!$url) return false;
    $file_row = sql_fetch(" select bf_file from {$g5['board_file_table']} where bo_table='".sql_real_escape_string($bo_table)."' and wr_id='".(int)$wr_id."' and bf_no=0 limit 1 ");
    if (!empty($file_row['bf_file']) && is_file(G5_DATA_PATH.'/file/'.$bo_table.'/'.$file_row['bf_file'])) return false;

    $image = skymulti_tiktok_download($url, 15, 'https://www.tiktok.com/');
    $info = function_exists('getimagesizefromstring') ? @getimagesizefromstring($image) : false;
    if (!$info) throw new Exception('Downloaded data is not a supported image.');
    $is_webp = defined('IMAGETYPE_WEBP') && $info[2] == constant('IMAGETYPE_WEBP');
    $ext = ($info[2] == IMAGETYPE_PNG) ? 'png' : (($info[2] == IMAGETYPE_GIF) ? 'gif' : ($is_webp ? 'webp' : 'jpg'));
    $upload_dir = G5_DATA_PATH.'/file/'.$bo_table;
    if (!is_dir($upload_dir)) { @mkdir($upload_dir, G5_DIR_PERMISSION, true); @chmod($upload_dir, G5_DIR_PERMISSION); }
    $filename = (int)$wr_id.'_'.substr(md5($video_id), 0, 10).'.'.$ext;
    $dest = $upload_dir.'/'.$filename;
    if (@file_put_contents($dest, $image, LOCK_EX) === false) throw new Exception('Cannot write thumbnail file.');
    @chmod($dest, G5_FILE_PERMISSION);

    if (!empty($file_row['bf_file'])) {
        sql_query(" delete from {$g5['board_file_table']} where bo_table='".sql_real_escape_string($bo_table)."' and wr_id='".(int)$wr_id."' and bf_no=0 ");
    }
    sql_query(" insert into {$g5['board_file_table']} set bo_table='".sql_real_escape_string($bo_table)."', wr_id='".(int)$wr_id."', bf_no=0, bf_source='tiktok_thumb.{$ext}', bf_file='{$filename}', bf_download=0, bf_content='', bf_filesize='".(int)filesize($dest)."', bf_width='".(int)$info[0]."', bf_height='".(int)$info[1]."', bf_type='".(int)$info[2]."', bf_datetime='".G5_TIME_YMDHIS."' ");
    sql_query(" update {$write_table} set wr_file=1 where wr_id='".(int)$wr_id."' ");
    return true;
}

function skymulti_tiktok_sync($options = array()) {
    global $g5, $config;
    $settings = include G5_DATA_PATH.'/tiktok_rss.config.php';
    if (empty($settings['enabled'])) throw new Exception('RSS synchronization is disabled.');
    $bo_table = isset($options['bo_table']) ? $options['bo_table'] : $settings['bo_table'];
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $bo_table)) throw new Exception('Invalid board name.');
    $timeout = isset($settings['timeout']) ? (int)$settings['timeout'] : 20;
    $board = sql_fetch(" select * from {$g5['board_table']} where bo_table='".sql_real_escape_string($bo_table)."' ");
    if (empty($board['bo_table'])) throw new Exception('TikTok board was not found.');
    $write_table = $g5['write_prefix'].$bo_table;

    $run_dir = G5_DATA_PATH.'/tiktok_rss';
    if (!is_dir($run_dir)) { @mkdir($run_dir, G5_DIR_PERMISSION, true); @chmod($run_dir, G5_DIR_PERMISSION); }
    $lock = @fopen($run_dir.'/sync.lock', 'c');
    if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) throw new Exception('A synchronization is already running.');

    try {
        $xml_data = skymulti_tiktok_download($settings['rss_url'], $timeout);
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xml_data, 'SimpleXMLElement', LIBXML_NOCDATA);
        if ($xml === false) throw new Exception('Invalid RSS XML.');
        $entries = isset($xml->channel->item) ? $xml->channel->item : $xml->entry;
        $items = array();
        if ($entries) foreach ($entries as $entry) {
            $link = trim((string)$entry->link);
            if (!$link && isset($entry->link['href'])) $link = trim((string)$entry->link['href']);
            $id = skymulti_tiktok_video_id($link);
            if (!$id) continue;
            $raw_desc = (string)$entry->description;
            if (!$raw_desc && isset($entry->content)) $raw_desc = (string)$entry->content;
            $published = (string)$entry->pubDate;
            if (!$published && isset($entry->published)) $published = (string)$entry->published;
            $timestamp = strtotime($published);
            if (!$timestamp) $timestamp = time();
            $thumb = '';
            $ns = $entry->getNamespaces(true);
            if (isset($ns['media'])) {
                $media = $entry->children($ns['media']);
                if (isset($media->content['url'])) $thumb = (string)$media->content['url'];
                elseif (isset($media->thumbnail['url'])) $thumb = (string)$media->thumbnail['url'];
            }
            if (!$thumb && preg_match('/<img[^>]+src=["\']([^"\']+)/i', $raw_desc, $m)) $thumb = html_entity_decode($m[1], ENT_QUOTES, 'UTF-8');
            $desc = trim(strip_tags($raw_desc));
            $content = '<div style="position:relative;padding-bottom:177.77%;height:0;overflow:hidden;margin-bottom:20px;"><iframe src="https://www.tiktok.com/embed/v2/'.$id.'" style="position:absolute;top:0;left:0;width:100%;height:100%;" frameborder="0" allowfullscreen></iframe></div>'.nl2br($desc);
            $items[] = array('id'=>$id, 'title'=>trim((string)$entry->title), 'link'=>$link, 'content'=>$content, 'thumb'=>$thumb, 'timestamp'=>$timestamp);
        }
        usort($items, function($a, $b) { return $a['timestamp'] - $b['timestamp']; });
        $count = 0;
        $repaired = 0;
        $upload_dir = G5_DATA_PATH.'/file/'.$bo_table;
        if (!is_dir($upload_dir)) { @mkdir($upload_dir, G5_DIR_PERMISSION, true); @chmod($upload_dir, G5_DIR_PERMISSION); }
        $admin_id = isset($config['cf_admin']) ? $config['cf_admin'] : '';
        $admin = $admin_id ? get_member($admin_id) : array();
        foreach ($items as $item) {
            $id_sql = sql_real_escape_string($item['id']);
            $exists = sql_fetch(" select wr_id from {$write_table} where wr_link1 like '%{$id_sql}%' limit 1 ");
            if (!empty($exists['wr_id'])) {
                try {
                    if (skymulti_tiktok_save_thumbnail($bo_table, $write_table, $exists['wr_id'], $item['id'], $item['thumb'])) {
                        $repaired++;
                        skymulti_tiktok_log('thumbnail repaired for '.$item['id'].' (wr_id '.$exists['wr_id'].')');
                    }
                } catch (Exception $repair_error) {
                    skymulti_tiktok_log('thumbnail repair failed for '.$item['id'].': '.$repair_error->getMessage());
                }
                continue;
            }
            $wr_num = get_next_num($write_table);
            $pubdate = date('Y-m-d H:i:s', $item['timestamp']);
            $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
            $sql = " insert into {$write_table} set wr_num='{$wr_num}', wr_reply='', wr_comment=0, ca_name='TikTok', wr_option='html1', wr_subject='".sql_real_escape_string($item['title'])."', wr_content='".sql_real_escape_string($item['content'])."', wr_link1='".sql_real_escape_string($item['link'])."', wr_link2='', wr_hit=0, wr_good=0, wr_nogood=0, mb_id='".sql_real_escape_string($admin_id)."', wr_password='".sql_real_escape_string(isset($admin['mb_password']) ? $admin['mb_password'] : '')."', wr_name='".sql_real_escape_string($board['bo_subject'])."', wr_email='', wr_homepage='', wr_datetime='{$pubdate}', wr_file=0, wr_last='".G5_TIME_YMDHIS."', wr_ip='".sql_real_escape_string($ip)."' ";
            sql_query($sql);
            $wr_id = sql_insert_id();
            sql_query(" update {$write_table} set wr_parent='{$wr_id}' where wr_id='{$wr_id}' ");
            if ($item['thumb']) {
                try {
                    skymulti_tiktok_save_thumbnail($bo_table, $write_table, $wr_id, $item['id'], $item['thumb']);
                } catch (Exception $thumb_error) {
                    skymulti_tiktok_log('thumbnail failed for '.$item['id'].': '.$thumb_error->getMessage());
                }
            }
            sql_query(" insert into {$g5['board_new_table']} (bo_table, wr_id, wr_parent, bn_datetime, mb_id) values ('{$bo_table}', '{$wr_id}', '{$wr_id}', '{$pubdate}', '".sql_real_escape_string($admin_id)."') ");
            $count++;
        }
        if ($count) sql_query(" update {$g5['board_table']} set bo_count_write=bo_count_write+".(int)$count." where bo_table='{$bo_table}' ");
        skymulti_tiktok_log('success: '.$count.' new item(s), '.$repaired.' thumbnail(s) repaired, '.count($items).' feed item(s)');
        flock($lock, LOCK_UN); fclose($lock);
        return array('ok'=>true, 'inserted'=>$count, 'repaired'=>$repaired, 'fetched'=>count($items));
    } catch (Exception $e) {
        skymulti_tiktok_log('error: '.$e->getMessage());
        flock($lock, LOCK_UN); fclose($lock);
        throw $e;
    }
}
