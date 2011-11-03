SELECT sum(t1.value),s5.code,max(s5.name) FROM
(SELECT * FROM value as v
-- entry = 5 or 6
LEFT JOIN value_in_set as vis1
ON v.id = vis1.value_id
LEFT JOIN "set" as s1
ON vis1.set_code = s1.code and vis1.set_kind_code = s1.set_kind_code
-- entry = 5 or 6
WHERE s1.code IN 
(WITH RECURSIVE included(sub_set_code,sub_set_kind_code, sup_set_code, sup_set_kind_code) AS (
    SELECT sub_set_code,sub_set_kind_code, sup_set_code, sup_set_kind_code FROM subset_in_set 
    WHERE (sup_set_code = '5' AND sup_set_kind_code = 'entry') OR (sup_set_code = '6' AND sup_set_kind_code = 'entry') 
  UNION ALL
    SELECT p.sub_set_code,p.sub_set_kind_code, p.sup_set_code, p.sup_set_kind_code
    FROM included pr, subset_in_set p
    WHERE p.sup_set_code = pr.sub_set_code AND p.sup_set_kind_code = p.sub_set_kind_code
)
SELECT sub_set_code
FROM included) AND s1.set_kind_code = 'entry') as t1
JOIN
-- year = 2010
(SELECT * FROM value as v
-- year = 2010
JOIN value_in_set as vis2
ON v.id = vis2.value_id
LEFT JOIN "set" as s2
ON vis2.set_code = s2.code and vis2.set_kind_code = s2.set_kind_code
-- year = 2010
WHERE s2.code ='2010' AND s2.set_kind_code = 'year') as t2
ON t1.id = t2.id
JOIN
-- column ='final_budget'
(SELECT * FROM value as v
-- column ='final_budget'
JOIN value_in_set as vis3
ON v.id = vis3.value_id
LEFT JOIN "set" as s3
ON vis3.set_code = s3.code and vis3.set_kind_code = s3.set_kind_code
-- column ='final_budget'
WHERE s3.code ='approved_budget' AND s3.set_kind_code = 'column') as t3
ON t1.id = t3.id
JOIN

-- organization
(SELECT *, s4.set_kind_code as s4_set_kind_code, s4.code as s4_code FROM value as v
-- organization
JOIN value_in_set as vis4
ON v.id = vis4.value_id
LEFT JOIN "set" as s4
ON vis4.set_code = s4.code and vis4.set_kind_code = s4.set_kind_code
-- organization
WHERE  s4.set_kind_code = 'organization') as t4
ON t1.id = t4.id

-- chapter
LEFT JOIN subset_in_set as sis
ON t4.s4_code = sis.sub_set_code and sis.sub_set_kind_code = t4.s4_set_kind_code
LEFT JOIN "set" as s5
ON sis.sup_set_code = s5.code and sis.sup_set_kind_code = s5.set_kind_code


GROUP BY s5.code

ORDER BY sum desc
