# skymulti

카페24 호스팅에서 운영 중인 그누보드5(영카트) 기반 쇼핑몰: http://www.skymulty.co.kr/

## 로컬 개발 환경 (Docker)

```bash
docker compose up -d
```

- 사이트: http://localhost:8080
- DB: `localhost:3306` (root / root)

최초 실행 전:

1. `data/dbconfig.php.example`를 `data/dbconfig.php`로 복사하고, DB 접속정보를 `docker-compose.yml`의 `db` 서비스 값(`skymulti` / `local_dev_password`)에 맞게 수정합니다.
2. 카페24에서 받은 DB 덤프(`.sql`)를 `docker/db-init/`에 넣으면 컨테이너 최초 기동 시 자동으로 임포트됩니다.

`data/`는 업로드 파일·캐시·세션 등을 담는 폴더라 git으로 관리하지 않습니다. 운영 서버의 `data/`를 그대로 내려받아 로컬 `data/`에 채워 넣어야 이미지 등이 정상적으로 보입니다.

## 배포 (GitHub Actions → 카페24 FTP)

`main` 브랜치에 push하면 `.github/workflows/deploy.yml`이 카페24 서버로 자동 업로드합니다. `data/`, `.sql`, Docker 관련 파일은 배포 대상에서 제외되어 운영 서버의 업로드 데이터를 덮어쓰지 않습니다.

저장소 설정에서 아래 Secrets를 등록해야 합니다 (Settings → Secrets and variables → Actions):

- `FTP_SERVER`: 카페24 FTP 호스트 주소
- `FTP_USERNAME`: FTP 계정
- `FTP_PASSWORD`: FTP 비밀번호

카페24가 FTPS(암호화 FTP)를 지원하지 않는다면 `deploy.yml`의 `protocol` 값을 `ftp`로 변경하세요.
