# Chinese Checkers using NodeJS, ExpressJS, ElephantIO
- Created an online board game using client-server architecture which provided a secure two-way communication and prevented cheating
- Addressed various security flaws like SQL injection, Input validation, URL injection, Buffer overflow, Database encryption, SSL, DOS etc.
- The results were observed through a vulnerability detection tool OWASP ZAP and the results were positive

## Installation and Usage

- Go ahead and install <a href="http://www.wampserver.com/en/">wamp server</a>

- Clone this repository inside your disk under the www folder inside wamp directory.

Note that : `The wamp directory is created default in C drive`

```sh
$ git clone https://github.com/spyashu/Chinese-checkers-using-client-server-architecture.git && cd www
```
- Now you can run the node server using
```sh
$ node NodeServer/server.js
```
- Now run the wamp engine and run the application on localhost:3000
