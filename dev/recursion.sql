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
