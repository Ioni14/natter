start:
	symfony serve -d --no-tls
	$(shell dr --network=host -u 0 -e POSTGRES_PASSWORD=root -d postgres)
	sleep 5 && bin/console d:d:create && bin/console d:m:migrate -n
