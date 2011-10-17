-- BudovaniStatu.cz, based on
-- KohoVolit.eu Generación Cuarta
-- tables of package GROUP

create table group_kind
(
	code varchar primary key,
	"name" varchar not null,
	short_name varchar,
	description text,
	subkind_of varchar references group_kind on delete cascade on update cascade,
	weight real
);

create table "group"
(
	id serial primary key,
	"name" varchar not null,
	short_name varchar,
	group_kind_code varchar not null references group_kind on delete restrict on update cascade,
	subgroup_of integer references "group" on delete cascade on update cascade,
	last_updated_on timestamp not null default current_timestamp,
	unique ("name", group_kind_code)
);

create table "role"
(
	code varchar primary key,
	name varchar not null,
	description text
);

/*create table party
(
	id serial primary key,
	"name" varchar not null,
	short_name varchar,
	description text,
	country_code varchar not null references country on delete restrict on update cascade,
	last_updated_on timestamp not null default current_timestamp,
	unique ("name", country_code)
);*/

create table organization_in_group
(
	organization_id integer references organization on delete cascade on update cascade,
	group_id integer references "group" on delete cascade on update cascade,
	role_code varchar references "role" on delete restrict on update cascade default 'member' ,
	since timestamp with time zone not null default '-infinity',
	until timestamp with time zone not null default 'infinity',
	primary key (organization_id, group_id, role_code, since),
	check (since <= until)
);

-- attributes
create table group_kind_attribute
(
	group_kind_code varchar references group_kind on delete cascade on update cascade,
	primary key (group_kind_code, "name", lang, since),
	foreign key (lang) references "language" on delete restrict on update cascade
) inherits ("attribute");

create table group_attribute
(
	group_id integer references "group" on delete cascade on update cascade,
	primary key (group_id, "name", lang, since),
	foreign key (lang) references "language" on delete restrict on update cascade
) inherits ("attribute");

create table role_attribute
(
	role_code varchar references "role" on delete cascade on update cascade,
	primary key (role_code, "name", lang, since),
	foreign key (lang) references "language" on delete restrict on update cascade
) inherits ("attribute");

/*create table party_attribute
(
	party_id integer references party on delete cascade on update cascade,
	parl varchar references parliament on delete restrict on update cascade default '-',
	primary key (party_id, "name", lang, parl, since),
	foreign key (lang) references "language" on delete restrict on update cascade
) inherits ("attribute");
*/

-- indexes (except PRIMARY KEY and UNIQUE constraints, for which the indexes have been created automatically)
create index organization_in_group_group_id on organization_in_group(group_id);
/*create index mp_in_group_constituency_id on mp_in_group(constituency_id);*/

-- privileges on objects
grant select
	on table group_kind, "group", "role", organization_in_group, group_kind_attribute, group_attribute, role_attribute
	to bs_user, bs_editor, bs_admin;
grant insert, update, delete, truncate
	on table group_kind, "group", "role", organization_in_group, group_kind_attribute, group_attribute, role_attribute
	to bs_admin;
grant usage
	on sequence group_id_seq
	to bs_admin;
