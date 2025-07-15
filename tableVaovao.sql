
INSERT INTO tabHistor (id, employee, salaire, dateAncien)
VALUES (
    id:int,
    'employee:varchar',
    'salaire:float',
    'dateAncien:date'
  );

ALTER TABLE tabHistor
ADD column dateAncien date NULL;