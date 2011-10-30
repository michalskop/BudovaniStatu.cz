-- BudovaniStatu.cz, based on
-- KohoVolit.eu Generación Cuarta
-- inserts into tables of package GROUP

insert into group_kind (code, "name", short_name, description, subkind_of, weight) values
('national_budget', 'National Budget','Budget', 'National budget.', null, 0.5),
('chapter', 'Chapter', 'Chapter', 'Chapter of national budget', 'national_budget', 1.0),
('region', 'Region', 'Region', 'Region', 'national_budget', 1.5);
('organization', 'Organization', 'Organization', 'Organization', 'chapter', 2.0);

insert into group_kind_attribute (group_kind_code, lang, "name", "value") values
('national_budget', 'cs', 'name', 'Státní rozpočet'),
('national_budget', 'cs', 'short_name', 'Rozpočet'),
('national_budget', 'cs', 'description', 'Státní rozpočet'),
('chapter', 'cs', 'name', 'Kapitola státního rozpočtu'),
('chapter', 'cs', 'short_name', 'Kapitola'),
('chapter', 'cs', 'description', 'Kapitola státního rozpočtu.'),
('organization', 'cs', 'name', 'Organizace'),
('organization', 'cs', 'short_name', 'Organizace'),
('organization', 'cs', 'description', 'Organizace.');

insert into "group" ("name", short_name, group_kind_code) values
('Czech Republic', 'Czechia', 'region');

insert into role (code, name) values
('member','Member');
