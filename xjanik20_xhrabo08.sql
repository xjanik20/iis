/*
* Soubor: xjanik20_xhrabo08.sql
* N�zev:  IIS Projekt 1
* Auto�i: Roman Jan�k, Michal Hrabovsk�
* Konvertov�no do MySQL
*/

DROP TABLE  StudentPredmet CASCADE;
DROP TABLE  UcitelPredmet CASCADE;
DROP TABLE  Otazka CASCADE;
DROP TABLE  Termin CASCADE;
DROP TABLE  Zkouska CASCADE;
DROP TABLE  Student CASCADE;
DROP TABLE  Predmet CASCADE;
DROP TABLE  Ucitel CASCADE;
DROP TABLE  Admin CASCADE;


CREATE TABLE Admin (
  id_ad INTEGER AUTO_INCREMENT,
  jmeno VARCHAR (100) NOT NULL,
  prijmeni VARCHAR (100) NOT NULL,
  mail VARCHAR(50) NOT NULL,
  login VARCHAR(30) NOT NULL,
  heslo VARCHAR(30) NOT NULL,
  CONSTRAINT PK_ad PRIMARY KEY (id_ad)
);

CREATE TABLE Ucitel (
  id_uc INTEGER AUTO_INCREMENT,
  jmeno VARCHAR (100) NOT NULL,
  prijmeni VARCHAR (100) NOT NULL,
  login VARCHAR(30) NOT NULL,
  heslo VARCHAR(30) NOT NULL,
  CONSTRAINT PK_uc PRIMARY KEY (id_uc)
);

CREATE TABLE Predmet (
  id_pr INTEGER AUTO_INCREMENT,
  nazev VARCHAR(100) NOT NULL UNIQUE,
  zkratka VARCHAR(3) NOT NULL UNIQUE,
  CONSTRAINT PK_pr PRIMARY KEY (id_pr)
);

CREATE TABLE Student (
  id_st INTEGER AUTO_INCREMENT,
  jmeno VARCHAR(100) NOT NULL,
  prijmeni VARCHAR(100) NOT NULL,
  login VARCHAR(30) NOT NULL,
  heslo VARCHAR(30) NOT NULL,
  CONSTRAINT PK_st PRIMARY KEY (id_st)
);

CREATE TABLE Zkouska (
  id_zk INTEGER AUTO_INCREMENT,
  jmeno VARCHAR (100) NOT NULL,
  max_studentu INTEGER DEFAULT 0 NOT NULL CHECK (max_studentu >= 0),
  max_bodu INTEGER DEFAULT 0 NOT NULL CHECK (max_bodu >= 0),
  min_bodu INTEGER DEFAULT 0 NOT NULL CHECK (min_bodu >= 0),
  pocet_otazek INTEGER DEFAULT 0 NOT NULL CHECK (pocet_otazek >= 0),
  typ_zkousky INTEGER NOT NULL,
  termin_cislo INTEGER NOT NULL CHECK (termin_cislo >= 0),
  
  id_pr INTEGER,
  CONSTRAINT PK_zk PRIMARY KEY (id_zk),
  CONSTRAINT FK_pr FOREIGN KEY (id_pr) REFERENCES Predmet(id_pr) 
);

CREATE TABLE  Termin (
  id_te INTEGER AUTO_INCREMENT,
  cas DATETIME NOT NULL,
  datum DATETIME NOT NULL,
  p_dosaz_bodu INTEGER DEFAULT 0 NOT NULL CHECK (p_dosaz_bodu >= 0),
  stav_zkousky TINYINT NOT NULL,
  komentar VARCHAR(100),
  dat_ohodnoceni DATETIME,
  
  id_uc INTEGER,
  id_st INTEGER,
  id_zk INTEGER,
  CONSTRAINT PK_te PRIMARY KEY (id_te),
  CONSTRAINT FK_uc FOREIGN KEY (id_uc)
  REFERENCES Ucitel(id_uc),
  CONSTRAINT FK_st FOREIGN KEY (id_st)
  REFERENCES Student(id_st),
  CONSTRAINT FK_zk FOREIGN KEY (id_zk)
  REFERENCES Zkouska(id_zk)
);

CREATE TABLE Otazka (
  id_ot INTEGER AUTO_INCREMENT,
  nazev VARCHAR(100) NOT NULL,
  pocet_bodu INTEGER DEFAULT 0 NOT NULL CHECK (pocet_bodu >= 0),
  
  id_te INTEGER,
  CONSTRAINT PK_ot PRIMARY KEY (id_ot),
  CONSTRAINT FK_te FOREIGN KEY (id_te)
  REFERENCES Termin(id_te)
);

CREATE TABLE UcitelPredmet
(
  id_uc INTEGER,
  id_pr INTEGER,
  CONSTRAINT PK_ucitelpredmet PRIMARY KEY
  (
      id_uc,
      id_pr
  ),
  CONSTRAINT FK_ucitel FOREIGN KEY (id_uc) REFERENCES Ucitel (id_uc),
  CONSTRAINT FK_predmet FOREIGN KEY (id_pr) REFERENCES Predmet (id_pr)
);

CREATE TABLE StudentPredmet
(
  id_st INTEGER,
  id_pr INTEGER,
  CONSTRAINT PK_studentpredmet PRIMARY KEY
  (
      id_st,
      id_pr
  ),
  CONSTRAINT FK_student FOREIGN KEY (id_st) REFERENCES Student (id_st),
  CONSTRAINT FK_predmet2 FOREIGN KEY (id_pr) REFERENCES Predmet (id_pr)
);



INSERT INTO Admin
VALUES(NULL, 'Tomáš', 'Sedláček', 'sedlacek@dummy.cz', 'admin', 'iis2017');

INSERT INTO Ucitel
VALUES(NULL, 'Jaroslav', 'Zendulka', 'zendu', '1234');
INSERT INTO Ucitel
VALUES(NULL, 'Bohuslav', 'K�ena', 'krena', '1234');
INSERT INTO Ucitel
VALUES(NULL, 'Zbyn�k', 'K�ivka', 'krivk', '1234');
INSERT INTO Ucitel
VALUES(NULL, 'Vladim�r', 'Bart�k', 'barti', '1234');

INSERT INTO Predmet
VALUES(NULL, 'Datab�zov� syst�my', 'IDS');
INSERT INTO Predmet
VALUES(NULL, 'Po��ta�ov� komunikace a s�t�', 'IPK');
INSERT INTO Predmet
VALUES(NULL, 'Principy programovac�ch jazyk� a OOP', 'IPP');
INSERT INTO Predmet
VALUES(NULL, 'Z�klady um�l� inteligence', 'IZU');

INSERT INTO Student
VALUES(NULL, 'Kamil', 'Vachrlat�', 'xvach', '1234');
INSERT INTO Student
VALUES(NULL, 'Jan', 'Nejezchleba', 'xneje', '1234');
INSERT INTO Student
VALUES(NULL, 'Tom�', 'Marn�', 'xmarn', '1234');
INSERT INTO Student
VALUES(NULL, 'Jarmil', 'Sychrav�', 'xsych', '1234');
INSERT INTO Student
VALUES(NULL, 'Hubert', 'Zendulka', 'xzend', '1234');

INSERT INTO Zkouska
VALUES(NULL, 'Semestr�ln� zkou�ka', 600, 60, 27, 10, 1, 1, 1);
INSERT INTO Zkouska
VALUES(NULL, 'Semestr�ln� zkou�ka', 300, 60, 15, 15, 1, 3, 1);
INSERT INTO Zkouska
VALUES(NULL, 'P�lsemestr�ln� zkou�ka', 13, 20, 0, 5, 2, 1, 3);
INSERT INTO Zkouska
VALUES(NULL, 'Semestr�ln� zkou�ka', 450, 70, 35, 13, 1, 2, 2);


INSERT INTO Termin
VALUES(NULL, (STR_TO_DATE('16:18:14', '%H:%i:%s')), (STR_TO_DATE('20122112', '%Y%d%m')), 40, 2, 'V�te�n�', (NOW()), 1, 1, 1);
INSERT INTO Termin
VALUES(NULL, (STR_TO_DATE('08:00:00', '%H:%i:%s')), (STR_TO_DATE('00330304', '%Y%d%m')), 20, 3, NULL, NULL, 1, 2, 2);
INSERT INTO Termin
VALUES(NULL, (STR_TO_DATE('10:30:00', '%H:%i:%s')), (STR_TO_DATE('20152005', '%Y%d%m')), DEFAULT, 0, NULL, NULL, 2, 1, 3);
INSERT INTO Termin
VALUES(NULL, (STR_TO_DATE('12:00:00', '%H:%i:%s')), (STR_TO_DATE('20170505', '%Y%d%m')), 15, 1, 'Nic moc', (NOW()), 3, 3, 3);
INSERT INTO Termin
VALUES(NULL, (STR_TO_DATE('12:00:00', '%H:%i:%s')), (STR_TO_DATE('20170505', '%Y%d%m')), 15, 1, 'Nic moc', (NOW()), 3, 3, 2);

INSERT INTO Otazka
VALUES(NULL, 'Co je to syntaxe?', 9999, 2);
INSERT INTO Otazka
VALUES(NULL, 'Uve�te funkcion�ln� programovac� jazyk.', 2, 1);
INSERT INTO Otazka
VALUES(NULL, 'Jak prob�h� multicastov� komunikace?', 5, 4);
INSERT INTO Otazka
VALUES(NULL, 'Prove�te normalizaci na 2NF', 10, 2);

INSERT INTO UcitelPredmet
VALUES(1, 2);
INSERT INTO UcitelPredmet
VALUES(1, 1);
INSERT INTO UcitelPredmet
VALUES(2, 2);
INSERT INTO UcitelPredmet
VALUES(3, 4);

INSERT INTO StudentPredmet
VALUES(1, 2);
INSERT INTO StudentPredmet
VALUES(1, 3);
INSERT INTO StudentPredmet
VALUES(4, 2);
INSERT INTO StudentPredmet
VALUES(3, 1);
    

-- vyp�e u�itele a p�edm�ty, kter� u��; slou�� k zobrazen� tabulky p�edm�t� a u�itel�
SELECT jmeno, prijmeni, nazev, zkratka
FROM Ucitel NATURAL JOIN UcitelPredmet NATURAL JOIN Predmet;

-- vyp�e �daje o jednotliv�ch sezen�ch; slou�� k zobrazen� tabulky sezen� s maximim�ln�m po�tem student� na zkou�ku
SELECT jmeno, termin_cislo AS "Cislo Sezeni", COUNT(id_zk) AS "Pocet Prihlasenych", max_studentu AS "maximum studentu"
FROM Zkouska JOIN Termin USING(id_zk,id_zk)
GROUP BY jmeno, termin_cislo, max_studentu;
  
-- vybere studenty, kte�� nemaj� nenulov� hodnocen� (�patn� prosp�ch); slou�� k statistice student�  
SELECT jmeno, prijmeni
FROM Student
WHERE id_st NOT IN(
  SELECT id_st 
  FROM Termin
  WHERE p_dosaz_bodu <> 0
);

-- Dotaz spo��t� celkov� dosa�en� po�et z ot�zek 1 term�nu; v aplikaci se hodnota po odesl�n� hodnocen� u�itelem ulo�� do polo�ky p_dosaz_bodu v tabulce Term�n
SELECT id_te, SUM(pocet_bodu) as bodu
FROM Otazka
GROUP BY id_te;
  
-- vyp�e p�edm�ty, kter� maj� term�n ve stejn� datum; v aplikaci bude slou�it pro upozorn�n� u�itel� na kolizi datum� term�n� zkou�ek
SELECT Predmet.Nazev, Termin.datum FROM (Predmet NATURAL JOIN Zkouska) JOIN Termin USING(id_zk,id_zk)
WHERE Termin.datum IN(
  SELECT datum FROM (
    SELECT COUNT(DISTINCT id_pr) AS duplicit, datum FROM Zkouska NATURAL JOIN Termin
    GROUP BY datum
  )
  WHERE duplicit > 1
);

-- Vyp�e p�edm�ty, kter� nemaj� zkou�ku; v aplikaci bude slou�it k identifikaci p�edm�t� bez zkou�ky
SELECT nazev FROM Predmet
WHERE NOT EXISTS(
  SELECT * FROM Zkouska
  WHERE Predmet.id_pr = Zkouska.id_pr
);

-- SQL 4. ��st---------------------------------------------------------------------------------------------------
-- triggery
-- P�i pokusu o vlo�en� NULL hodnoty prim�rn�ho kl��e vlo�� n�sleduj�c� hodnotu sekvence
CREATE OR REPLACE TRIGGER Generuj_klic
  BEFORE INSERT ON Otazka
  FOR EACH ROW
  BEGIN
    IF :NEW.id_ot IS NULL THEN
      SET :NEW.id_ot = NULL;
    END IF;
  END;
/  
-- demonstra�n� dotazy
INSERT INTO Otazka
VALUES(NULL, 'Quo vadis?', 10, 3);
SELECT * FROM Otazka;


-- P�i zm�n� stavu term�nu na "ohodnoceno" - 3, se nastav� datum ohodnocen� na aktu�ln� datum
CREATE OR REPLACE TRIGGER Generuj_datum
  BEFORE UPDATE ON Termin
  FOR EACH ROW
  BEGIN
    IF :NEW.stav_zkousky = 3 AND :NEW.dat_ohodnoceni IS NULL THEN
      SET :NEW.dat_ohodnoceni = SYSDATE();
    END IF;
  END;
/
-- demonstra�n� dotazy
SELECT * FROM Termin where id_te = 2;
UPDATE Termin SET stav_zkousky = 3 WHERE id_te = 2;
SELECT * FROM Termin where id_te = 2;

  
-- Se�te po�ty bod� z ot�zek pat��c�ch k 1 term�nu vlo�� je do p_dosaz_bodu
DROP PROCEDURE IF EXISTS insert_body;

DELIMITER //

CREATE PROCEDURE insert_body(IN curr_id_te INTEGER)
 BEGIN
  DECLARE res VARCHAR(4000) /* Use -meta option Otazka.id_te%TYPE */  DEFAULT  0;
DECLARE NOT_FOUND INT DEFAULT 0;
  DECLARE otazka_cursor CURSOR FOR                         
    SELECT pocet_bodu FROM Otazka
    WHERE id_te = curr_id_te;

 DECLARE CONTINUE HANDLER FOR NOT FOUND SET NOT_FOUND = 1;     
  DECLARE otazka_rec CURSOR FOR otazka_cursor OPEN otazka_rec;
 FETCH otazka_rec INTO;
 WHILE NOT_FOUND=0
 DO
    SET res = res + otazka_rec.pocet_bodu;
  FETCH otazka_cursor INTO;
  END WHILE;
  CLOSE otazka_cursor;
  UPDATE Termin
    SET p_dosaz_bodu = res
    WHERE id_te = curr_id_te;
END;
//

DELIMITER ;


-- demonstra�n� dotazy
select * from termin;
set @stmt_str =  insert_body(2);
prepare stmt from @stmt_str;
execute stmt;
deallocate prepare stmt;
select * from otazka;
select * from zkouska;


-- procedura vrac� pr�m�r konkr�tn� zkou�ky
DROP PROCEDURE IF EXISTS get_average;

DELIMITER //

CREATE PROCEDURE get_average(IN id_zkousky INTEGER, OUT prumer DOUBLE)
 BEGIN
  DECLARE soucet INTEGER  DEFAULT  0;
  DECLARE pocet_terminu INTEGER  DEFAULT  0;
DECLARE NOT_FOUND INT DEFAULT 0;
  DECLARE termin_cursor CURSOR FOR                             
    SELECT p_dosaz_bodu FROM Termin 
    WHERE id_zk = id_zkousky;

 DECLARE CONTINUE HANDLER FOR NOT FOUND SET NOT_FOUND = 1;  
   DECLARE EXIT HANDLER FOR ZERO_DIVIDE BEGIN 
      SET prumer = 0;
   END;
  DECLARE termin_rec CURSOR FOR termin_cursor OPEN termin_rec;
 FETCH termin_rec INTO;
 WHILE NOT_FOUND=0
 DO
    SET soucet = soucet + termin_rec.p_dosaz_bodu;
    SET pocet_terminu = pocet_terminu + 1;
  FETCH termin_cursor INTO;
  END WHILE;
  CLOSE termin_cursor;
  SET prumer = 1.0 * soucet / pocet_terminu;
END;
//

DELIMITER ;



-- vytiskne pr�m�r
DROP PROCEDURE IF EXISTS print_average;

DELIMITER //

CREATE PROCEDURE print_average(IN id_zkousky INTEGER)
 BEGIN
  DECLARE prumer DOUBLE  DEFAULT  0;
 
  get_average(id_zkousky, prumer);
  PUT_LINE (TO_CHAR(prumer));
END;
//

DELIMITER ;


-- demonstrace
SET @stmt_str =  print_average(2);
PREPARE stmt FROM @stmt_str;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- P��stupov� pr�va
GRANT INSERT,SELECT,UPDATE ON Otazka TO xhrabo08;
GRANT INSERT,SELECT,UPDATE ON Predmet TO xhrabo08;
GRANT INSERT,SELECT,UPDATE ON Student TO xhrabo08;
GRANT INSERT,SELECT,UPDATE ON StudentPredmet TO xhrabo08;
GRANT INSERT,SELECT,UPDATE ON Termin TO xhrabo08;
GRANT INSERT,SELECT,UPDATE ON Ucitel TO xhrabo08;
GRANT INSERT,SELECT,UPDATE ON UcitelPredmet TO xhrabo08;
GRANT INSERT,SELECT,UPDATE ON Zkouska TO xhrabo08;

-- materializovan� pohled pro druh�ho u�ivatele
DROP MATERIALIZED VIEW pohled_pro_druheho;
CREATE MATERIALIZED VIEW pohled_pro_druheho 
REFRESH ON DEMAND AS
  SELECT Predmet.nazev, Zkouska.jmeno, Zkouska.termin_cislo, datum, cas
  FROM Zkouska NATURAL JOIN Termin NATURAL JOIN Predmet;
  
-- demonstrace funk�nosti
SELECT * FROM pohled_pro_druheho;

UPDATE Termin
SET datum = (STR_TO_DATE('20170505', '%Y%d%m'))
WHERE DATE_FORMAT(datum, '%Y%d%m') = '20180505';

BEGIN
dbms_mview.refresh('pohled_pro_druheho');
END;
/
SELECT * FROM pohled_pro_druheho;


-- odstran�n� p�edchoz�ch index�
DROP INDEX foo_index;
DROP INDEX bar_index;

-- explain plan bez index�
EXPLAIN PLAN FOR
  SELECT COUNT(DISTINCT id_pr) AS duplicit, datum FROM Zkouska NATURAL JOIN Termin
  GROUP BY datum;
SELECT PLAN_TABLE_OUTPUT FROM TABLE(DBMS_XPLAN.DISPLAY());

-- index
CREATE INDEX foo_index ON Termin(id_zk,datum);
CREATE INDEX bar_index ON Zkouska(id_zk,id_pr);

-- explain plan s indexy
EXPLAIN PLAN FOR
  SELECT COUNT(DISTINCT id_pr) AS duplicit, datum FROM Zkouska NATURAL JOIN Termin
  GROUP BY datum;
SELECT PLAN_TABLE_OUTPUT FROM TABLE(DBMS_XPLAN.DISPLAY());