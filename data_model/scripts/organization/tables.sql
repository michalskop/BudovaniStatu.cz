-- BudovaniStatu.cz, based on
-- KohoVolit.eu Generación Cuarta
-- tables of package ORGANIZATION

create table organization
(
	id serial primary key,
	"name" varchar not null,
	short_name varchar,
	disambiguation varchar not null default '',
	last_updated_on timestamp not null default current_timestamp,
	unique ("name", disambiguation)
);

/*create table office
(
	mp_id integer references mp on delete cascade on update cascade,
	parliament_code varchar references parliament on delete restrict on update cascade,
	address varchar,
	phone varchar,
	latitude double precision,
	longitude double precision,
	relevance real,
	since timestamp with time zone not null default '-infinity',
	until timestamp with time zone not null default 'infinity',
	primary key (mp_id, parliament_code, address, since),
	check (since <= until)
);*/

-- attributes
create table organization_attribute
(
	organization_id integer references organization on delete cascade on update cascade,
	primary key (organization_id, "name", lang, since),
	foreign key (lang) references "language" on delete restrict on update cascade
) inherits ("attribute");

-- privileges on objects
grant select
	on table organization, organization_attribute
	to bs_user, bs_editor, bs_admin;
grant insert, update, delete, truncate
	on table organization, organization_attribute
	to bs_admin;
grant usage
	on sequence organization_id_seq
	to bs_admin;
