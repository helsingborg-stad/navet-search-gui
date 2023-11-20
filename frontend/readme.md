# Docker run
docker build --tag frontend:latest .
docker run -d -p 8091:80 frontend
docker push helsingborgstad/webui:tagname

## ENV vars: 
MS_AUTH        Auth service
MS_NAVET       Navet endpoint
MS_NAVET_AUTH  Navet auth key key