# Docker run
docker build --tag frontend:latest .
docker run -d -p 8091:80 frontend
docker push datatorgetpoc/webui:tagname

ENV vars: 
MS_BOOKING: Booking api
MS_USER: User api
MS_PAYMENT: Payment api

ENV vars defaults: 
'MS_BOOKING' => "http://193.180.109.41:30001", 
'MS_USER' => "http://193.180.109.41:30002",
'MS_PAYMENT' => "http://193.180.109.41:30004"