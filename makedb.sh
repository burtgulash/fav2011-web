#! /bin/bash

dbfile=data.db

>$dbfile

#users table
sqlite3 $dbfile "CREATE TABLE users (id INTEGER PRIMARY KEY, 
                     name TEXT, pass CHARACTER(32), permissions INT);"

sqlite3 $dbfile "INSERT INTO users (name, pass, permissions) values 
                           ('honza', 'd9479746ea51a4177f8bc092c5db7b8d', 1);"

sqlite3 $dbfile "INSERT INTO users (name, pass, permissions) values 
                           ('pepa', '281ffbf5eb2a3916b9c1eb8f28637836', 1);"

# news table
sqlite3 $dbfile "CREATE TABLE news (id INTEGER PRIMARY KEY, 
                                title TEXT, article TEXT, timeEntered DATE);"

sqlite3 $dbfile "INSERT INTO news (title, article, timeEntered) values
                           ('Založili jsme tým', 'Ano, je to tak, jsme borci.',
                             DATETIME('NOW'));"

sqlite3 $dbfile "INSERT INTO news (title, article, timeEntered) values
                           ('Jsme tym', 'Mame velky, maly tym, jo.',
                             DATETIME('NOW'));"
