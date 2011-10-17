-- BudovaniStatu.cz, based on
-- KohoVolit.eu Generación Cuarta
-- roles (users) creation

-- must be executed by a superuser (postgres)

create user bs_admin noinherit password 'bs_admin';
create user bs_user noinherit password 'bs_user';
create user bs_editor noinherit password 'bs_editor';
create role budovanistatu noinherit;

grant bs_admin, bs_user, bs_editor to michal;
grant budovanistatu to bs_admin;

revoke usage on language plpgsql from public;
grant usage on language plpgsql to budovanistatu, bs_admin;
