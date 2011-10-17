-- BudovaniStatu.cz, based on
-- KohoVolit.eu Generación Cuarta
-- database building

-- must be connected to the database budovanistatu
set role to budovanistatu;

comment on database budovanistatu is 'Project BudovaniStatu.cz.';

\i base/tables.sql
\i base/triggers.sql
\i base/inserts.sql
/*\i parliament/tables.sql
\i parliament/triggers.sql
\i parliament/inserts.sql*/
\i organization/tables.sql
\i organization/triggers.sql
\i group/tables.sql
\i group/triggers.sql
\i group/inserts.sql
/*\i wtt/tables.sql
\i wtt/functions.sql*/
