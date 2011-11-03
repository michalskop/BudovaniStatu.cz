SELECT sum(v."value")-0 as sum FROM
SELECT * FROM
"value" as v
LEFT JOIN
label_for_value as lfv
ON v.id = lfv.value_id
LEFT JOIN label as l
ON l.id = lfv.label_id
LEFT JOIN
label_for_value as lfv2
ON v.id = lfv2.value_id
LEFT JOIN label as l2
ON l2.id=lfv2.label_id

WHERE
l.label_kind_code = 'org_id' and l.name='13693131'
AND
l2.id IN
(SELECT * FROM
(WITH RECURSIVE included(sub_label_id,sup_label_id) AS (
    SELECT sub_label_id, sup_label_id FROM hierarchy WHERE sup_label_id = '293'
  UNION ALL
    SELECT p.sub_label_id, p.sup_label_id
    FROM included pr, hierarchy p
    WHERE p.sup_label_id = pr.sub_label_id
)
SELECT sub_label_id
FROM included
) as t)


-- ************************
-- final values:
SELECT * FROM hierarchy as h
LEFT JOIN label as l1
ON h.sub_label_id = l1.id
LEFT JOIN label as l2
ON h.sup_label_id = l2.id
LEFT JOIN 
(
SELECT v.value,l2.* FROM
"value" as v
LEFT JOIN
label_for_value as lfv
ON v.id = lfv.value_id
LEFT JOIN label as l
ON l.id = lfv.label_id

LEFT JOIN
label_for_value as lfv2
ON v.id = lfv2.value_id
LEFT JOIN label as l2
ON l2.id=lfv2.label_id

LEFT JOIN
label_for_value as lfv3
ON v.id = lfv3.value_id
LEFT JOIN label as l3
ON l3.id=lfv3.label_id

WHERE
l.label_kind_code = 'org_id' and l.name='117650' 
and l3.label_kind_code = 'column_code' and l3.name='final_budget'
and l2.label_kind_code ='entry_id'
) as t
ON t.id = l1.id
where h.tree_id = 1

-- insert csv file
COPY value_50 FROM '/home/michal/aris/central-40-50-3.csv' 
WITH DELIMITER E'\t' CSV HEADER QUOTE E'\''

-- chart_2
SELECT organization_code,max(o.name) as "name",max(o.short_name) as short_name,sum(value) FROM value_50 as v
LEFT JOIN organization as o
ON v.organization_code = o.code
WHERE year = 2010 
AND CAST(entry_code as integer) >= 5000 
AND CAST(entry_code as integer) < 7000
AND column_code = 'final_budget'
GROUP BY organization_code
ORDER BY sum DESC

-- 
