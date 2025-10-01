

# Documentation

###---  Regula --- 
####docs: https://docs.regulaforensics.com

Запуск сервера (port:41101) / обработка matchSelfie :  
- systemctl start face-rec-service  

Запуск сервера (port:8080) / обработка documentReader :
- systemctl start regula-document-reader-webapi.service

Создать config.yaml в /opt/regula/face-rec-service/config.yaml из config.yaml.example

Порт 8080 должен быть свободен на сервере, так как через него работает Regula


###--- Registration link ---
https://app.paymentiq.cc/kyc/63f599303000600668544ee6/160905 : prod
http://127.0.0.1:8138/kyc/63f599303000600668544ee6/160905 : local


###--- Create Docker network ---
docker network create --driver bridge --subnet=172.18.0.0/16 pg_web - create network
docker inspect <container name> - research container network
