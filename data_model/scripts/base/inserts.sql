-- BudovaniStatu.cz, based on
-- KohoVolit.eu Generación Cuarta
-- inserts into tables of package BASE

insert into "language" (code, "name", short_name, description, locale) values
('-', 'any language', 'any', 'This language is referenced as a foreign key by language neutral attributes.', 'C'),
('en', 'in English', 'English', null, 'en_US.UTF-8'),
('sk', 'po slovensky', 'slovenčina', null, 'sk_SK.UTF-8'),
('cs', 'česky', 'čeština', null, 'cs_CZ.UTF-8');
