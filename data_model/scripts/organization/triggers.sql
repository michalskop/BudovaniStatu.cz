-- BudovaniStatu.cz, based on
-- KohoVolit.eu Generación Cuarta
-- triggers of package ORGANIZATION
	
create or replace function organization_attribute_temporal_check()
returns trigger as $$
begin
	if tg_op = 'INSERT' then
		perform * from organization_attribute where (organization_id, "name", lang) = (new.organization_id, new."name", new.lang) and until > new.since and since < new.until limit 1;
	else  -- tg_op = 'UPDATE'
		perform * from organization_attribute where (organization_id, "name", lang) = (new.organization_id, new."name", new.lang) and until > new.since and since < new.until 
			and (organization_id, "name", lang, since) != (old.organization_id, old."name", old.lang, old.since)
			limit 1;
	end if;
	if found then
		raise exception 'Time period in the row (organization_id=%, name=''%'', value=''%'', lang=''%'', since=''%'', until=''%'') being inserted (or updated) into ORGANIZATION_ATTRIBUTE overlaps with a period of another value of the attribute.',
			new.organization_id, new."name", new."value", new.lang, new.since, new.until;
	end if;
	return new;
end; $$ language plpgsql;

create trigger organization_attribute_temporal_check
	before insert or update on organization_attribute
	for each row execute procedure organization_attribute_temporal_check();

create or replace function organization_archive_value(a_organization_id integer, a_column_name varchar, a_column_value varchar, a_update_date timestamp)
returns void as $$
declare
	l_since timestamp with time zone;
begin
	select until into l_since from organization_attribute where organization_id = a_organization_id and "name" = a_column_name and lang = '-' order by until desc limit 1;
	if not found then l_since = '-infinity'; end if;
	insert into organization_attribute(organization_id, "name", "value", since, until) values (a_organization_id, a_column_name, a_column_value, l_since, a_update_date);
end; $$ language plpgsql;

create or replace function organization_changed_values_archivation()
returns trigger as $$
begin
	if new.last_updated_on is null then new.last_updated_on = 'now'; end if;
	if new.last_updated_on < old.last_updated_on then return null; end if;
	if new.first_name is distinct from old.name then perform organization_archive_value(old.id, 'name', old.name, new.last_updated_on); end if;
	if new.disambiguation is distinct from old.disambiguation then perform organization_archive_value(old.id, 'disambiguation', old.disambiguation, new.last_updated_on); end if;
	return new;
end; $$ language plpgsql;

create trigger organization_changed_values_archivation
	before update on organization
	for each row execute procedure organization_changed_values_archivation();
