start:
	symfony serve --no-tls
	dr --network=host -u 0 -e POSTGRES_PASSWORD=root postgres
	bin/console d:d:create && bin/console d:m:migrate -n
