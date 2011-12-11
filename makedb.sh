#! /bin/bash

dbfile=data.db

>$dbfile

#users table
sqlite3 $dbfile "CREATE TABLE users (id INTEGER PRIMARY KEY, 
                     name TEXT, pass CHARACTER(32), permissions INT,
                     jmeno TEXT, prijmeni TEXT, telCislo TEXT);"

sqlite3 $dbfile "INSERT INTO users (name, pass, permissions, jmeno, prijmeni, 
                                                                      telCislo) 
                  values ('honza', 'd9479746ea51a4177f8bc092c5db7b8d', 1,
                           'Jan', 'Borůvka', '1234732');"

sqlite3 $dbfile "INSERT INTO users (name, pass, permissions, jmeno, prijmeni) 
                  values ('pepa', '281ffbf5eb2a3916b9c1eb8f28637836', 1,
                           'Josef', 'Bro');"

# news table
sqlite3 $dbfile "CREATE TABLE news (id INTEGER PRIMARY KEY, 
                                title TEXT, article TEXT, timeEntered DATE);"

sqlite3 $dbfile "INSERT INTO news (title, article, timeEntered) values
                           ('Založili jsme tým', 'Ano, je to tak, jsme borci.',
                             DATETIME('NOW'));"

sqlite3 $dbfile "INSERT INTO news (title, article, timeEntered) values
                           ('Jsme tym', 'Mame velky, maly tym, jo.',
                             DATETIME('NOW'));"

# scores table
sqlite3 $dbfile "CREATE TABLE scores (id INTEGER PRIMARY KEY, opponent TEXT,
                            ours INT, theirs INT, timePlayed DATE);"

sqlite3 $dbfile "INSERT INTO scores (opponent, ours, theirs, timePlayed) 
                        values ('Oponenté', '5', '3', JULIANDAY('2011-01-01'));"

sqlite3 $dbfile "INSERT INTO scores (opponent, ours, theirs, timePlayed) 
                        values ('Blbečči', '0', '1', DATE('NOW'));"

sqlite3 $dbfile "INSERT INTO scores (opponent, ours, theirs, timePlayed) 
                        values ('Dědkové', '1', '8', JULIANDAY('1977-08-05'));"