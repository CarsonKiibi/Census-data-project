DROP TABLE IF EXISTS Income;
DROP TABLE IF EXISTS Immigrant;
DROP TABLE IF EXISTS Person;
DROP TABLE IF EXISTS Career;
DROP TABLE IF EXISTS Education;
DROP TABLE IF EXISTS Household;
DROP TABLE IF EXISTS Residence;
DROP TABLE IF EXISTS Area;
DROP TABLE IF EXISTS Country;
DROP TABLE IF EXISTS CountryIn;
DROP TABLE IF EXISTS ExternalRegion;
DROP TABLE IF EXISTS InternalRegion;
DROP TABLE IF EXISTS Region;

CREATE TABLE Region(
    rid INTEGER PRIMARY KEY, 
    rname TEXT NOT NULL,
    gdp_per_capita INTEGER
) STRICT;

CREATE TABLE InternalRegion(
    rid INTEGER PRIMARY KEY,
    gdp_from_goods INTEGER,
    gdp_from_services INTEGER,
    FOREIGN KEY (rid)
        REFERENCES Region(rid)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) STRICT;

CREATE TABLE ExternalRegion(
    rid INTEGER PRIMARY KEY,
    distance INTEGER,
    FOREIGN KEY (rid)
        REFERENCES Region(rid)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) STRICT;

CREATE TABLE Country(
    cid INTEGER PRIMARY KEY,
    cname TEXT NOT NULL
) STRICT;

CREATE TABLE CountryIn(
    rid INTEGER,
    cid INTEGER,
    PRIMARY KEY (rid, cid),
    FOREIGN KEY (rid)
        REFERENCES ExternalRegion(rid)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (cid)
        REFERENCES Country(cid)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) STRICT;

CREATE TABLE Area(
    aid INTEGER PRIMARY KEY, 
    aname TEXT NOT NULL, 
    population_density REAL, 
    rid INTEGER NOT NULL,
    FOREIGN KEY(rid)
        REFERENCES Region(rid)
        ON DELETE NO ACTION
        ON UPDATE CASCADE
) STRICT;

CREATE TABLE Residence(
    rsid INTEGER PRIMARY KEY, 
    rtype TEXT, 
    rooms INTEGER, 
    bedrooms INTEGER, 
    cost INTEGER,
    aid INTEGER,
    FOREIGN KEY(aid)
        REFERENCES Area(aid)
        ON DELETE NO ACTION
        ON UPDATE CASCADE
) STRICT;

CREATE TABLE Household(
    hid INTEGER PRIMARY KEY, 
    htype TEXT, 
    size INTEGER, 
    rsid INTEGER NOT NULL, 
    ownership TEXT,
    FOREIGN KEY(rsid)
        REFERENCES Residence(rsid)
        ON DELETE NO ACTION
        ON UPDATE CASCADE
) STRICT;

CREATE TABLE Education(
    eid INTEGER PRIMARY KEY, 
    field_of_study TEXT, 
    attending TEXT, 
    highest_attainment TEXT, 
    rid INTEGER, 
    FOREIGN KEY(rid)
        REFERENCES Region(rid)
        ON DELETE NO ACTION
        ON UPDATE CASCADE
) STRICT;

CREATE TABLE Career(
    crid INTEGER PRIMARY KEY,
    industry TEXT,	
    occupation TEXT
) STRICT;

CREATE TABLE Person(
    pid INTEGER PRIMARY KEY, 
    marital_status TEXT, 
    gender TEXT, 
    age INTEGER,
    hid INTEGER NOT NULL, 
    crid INTEGER,
    eid INTEGER,
    FOREIGN KEY (hid) 
        REFERENCES Household(hid) 
        ON DELETE NO ACTION
        ON UPDATE CASCADE,
    FOREIGN KEY (eid) 
        REFERENCES Education(eid) 
        ON DELETE NO ACTION
        ON UPDATE CASCADE,
    FOREIGN KEY (crid) 
        REFERENCES Career(crid) 
        ON DELETE NO ACTION
        ON UPDATE CASCADE
) STRICT;

CREATE TABLE Immigrant(
    pid INTEGER PRIMARY KEY,
    rid INTEGER, 
    age_at_immigration INTEGER, 
    year_of_immigration INTEGER, 
    status TEXT,
    FOREIGN KEY (pid)
        REFERENCES Person(pid)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (rid)
        REFERENCES ExternalRegion(rid)
        ON DELETE NO ACTION
        ON UPDATE CASCADE
) STRICT;

CREATE TABLE Income(
    pid INTEGER,
    year INTEGER,
    total_income INTEGER, 
    investment_income INTEGER,
    net_capital_gains INTEGER,
    government_transfers INTEGER,
    employment_income INTEGER,
    income_tax INTEGER, 
    PRIMARY KEY (pid, year),
    FOREIGN KEY (pid)
        REFERENCES Person(pid)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) STRICT;
