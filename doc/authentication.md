## Basic

`Authorization: Basic <base64encoded "username:password">`

```
$ echo -n "username:password" | base64
dXNlcm5hbWU6cGFzc3dvcmQ=
$ curl -i -H 'Authorization: Basic dXNlcm5hbWU6cGFzc3dvcmQ=' https://localhost
$ curl -i -u username:password https://localhost
```

Le mot de passe est simplement encodé, il faut donc utiliser une connexion chiffrée (HTTPS) pour requêter le serveur.

## Les algorithmes de Hashing pour stocker les mots-de-passe

Construits pour, en pratique, ne pas pouvoir retrouver les valeurs en clairs à partir des hashs.

Algos modernes (fonctions de dérivations): Argon2, Scrypt, Bcrypt, PBKDF2

https://en.wikipedia.org/wiki/Key_derivation_function

### Salt

Le sel est une valeur aléatoire qui est mixée au mot de passe quand il est hashé.

Il permet d'avoir un hash toujours différent même avec un mot de passe identique. Cela contre les Rainbow-tables qui permet de retrouver facilement des mots de passe à partir des hashs communs.


