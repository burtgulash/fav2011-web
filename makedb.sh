#! /bin/bash

dbfile=data.db

>$dbfile

#users table
sqlite3 $dbfile "CREATE TABLE users (id INTEGER PRIMARY KEY, 
                     name TEXT, pass CHARACTER(32), permissions INT,
                     jmeno TEXT, prijmeni TEXT, telCislo TEXT, mesto TEXT);"

sqlite3 $dbfile "INSERT INTO users (name, pass, permissions, jmeno, prijmeni, 
                                               telCislo, mesto) 
                  values ('honza', 'd9479746ea51a4177f8bc092c5db7b8d', 1,
                           'Jan', 'Novák', '123-473-283', 'Praha');"

sqlite3 $dbfile "INSERT INTO users (name, pass, permissions, jmeno, prijmeni) 
                  values ('pepa', '281ffbf5eb2a3916b9c1eb8f28637836', 1,
                           'Josef', 'Pepík');"

sqlite3 $dbfile "INSERT INTO users (name, pass, permissions, jmeno, prijmeni,
                               telCislo, mesto) 
                  values ('jirka', '742f1e246d243ace2f7f4316c3fe6347', 1,
                           'Jiří', 'Zkřoví', '432-789-543', 'Praha');"

sqlite3 $dbfile "INSERT INTO users (name, pass, permissions, jmeno, prijmeni,
                               telCislo, mesto) 
                  values ('vladimir', '5ae21533f62bc2015c2092cff7304b92', 1,
                           'Vladimír', 'Čech', '124-833-000', 'Praha');"

sqlite3 $dbfile "INSERT INTO users (name, pass, permissions, jmeno, prijmeni,
                               telCislo, mesto) 
                  values ('tonik', 'bb762d660de946e8eabd0865a17d5333', 1,
                           'Toníček', 'Sousedovic', '000-010-000', 'Kroměříž');"

sqlite3 $dbfile "INSERT INTO users (name, pass, permissions, jmeno, prijmeni,
                               telCislo, mesto) 
                  values ('ponik', '04d1876f191593ea1dbc5095b0e9601d', 1,
                           'Poníček', 'Sousedovic', '000-011-010', 'Kroměříž');"

sqlite3 $dbfile "INSERT INTO users (name, pass, permissions, jmeno, prijmeni,
                               telCislo, mesto) 
                  values ('cenda', '867f2b4fe8afe665202f90df7dfccfe2', 1,
                           'Jenda', 'Čenda', '010-011-010', 'Kroměříž');"

sqlite3 $dbfile "INSERT INTO users (name, pass, permissions, jmeno, prijmeni,
                                            telCislo, mesto) 
                  values ('boss', 'ceb8447cc4ab78d2ec34cd9f11e4bed2', 2,
                           'Jarda', 'Šéf', '000-123-321', 'Brno');"

# news table
sqlite3 $dbfile "CREATE TABLE news (id INTEGER PRIMARY KEY, 
                                title TEXT, article TEXT, timeEntered DATE);"

sqlite3 $dbfile "INSERT INTO news (title, article, timeEntered) values
                           ('Založili jsme tým.', 
                            'Ano, založili jsme si fotbalový tým, je to tak.',
                             JULIANDAY('2008-03-09'));"

sqlite3 $dbfile "INSERT INTO news (title, article, timeEntered) values
						   ('První odehraný zápas', 'Máme za sebou první,
dokonce úspěšný zápas proti týmu Valaši z Brna. Výsledek tohoto zápasu a
dalších odehraných bude v sekci výsledky. Doufejme, že se bude jednat pouze
o výhry',
                             JULIANDAY('2009-04-11'));"

sqlite3 $dbfile "INSERT INTO news (title, article, timeEntered) values
						   ('Máme nového člena', 'Nový člen se jmenuje Vladimír
a tento statný Pražák nám bude posilou na příští zápasy.',
                             JULIANDAY('2010-11-22'));"

sqlite3 $dbfile "INSERT INTO news (title, article, timeEntered) values
						   ('Rozrůstá se nám tým', 'Členové Toník a Poník nám
zajistí nejlepší možnou obranu.',
                             JULIANDAY('2010-12-05'));"

sqlite3 $dbfile "INSERT INTO news (title, article, timeEntered) values
                           ('Pepa jde do útoku', 
                            'Pepa bude ode dneška útočník.',
                             JULIANDAY('2011-28-03'));"

sqlite3 $dbfile "INSERT INTO news (title, article, timeEntered) values
                           ('Honza si zlomil palec', 'Proto bude hrát v bráně.',
                             JULIANDAY('2011-02-02'));"

# scores table
sqlite3 $dbfile "CREATE TABLE scores (id INTEGER PRIMARY KEY, opponent TEXT,
                            ours INT, theirs INT, timePlayed DATE);"

sqlite3 $dbfile "INSERT INTO scores (opponent, ours, theirs, timePlayed) 
                        values ('Valaši', '3', '2', JULIANDAY('2011-02-19'));"

sqlite3 $dbfile "INSERT INTO scores (opponent, ours, theirs, timePlayed) 
                        values ('Oponenté', '5', '3', JULIANDAY('2011-01-08'));"

sqlite3 $dbfile "INSERT INTO scores (opponent, ours, theirs, timePlayed) 
                        values ('Horníci', '0', '1', JULIANDAY('2010-03-27'));"

sqlite3 $dbfile "INSERT INTO scores (opponent, ours, theirs, timePlayed) 
                        values ('Dědkové', '1', '8', JULIANDAY('2009-08-05'));"

sqlite3 $dbfile "INSERT INTO scores (opponent, ours, theirs, timePlayed) 
                        values ('Valaši', '3', '1', JULIANDAY('2009-04-11'));"
