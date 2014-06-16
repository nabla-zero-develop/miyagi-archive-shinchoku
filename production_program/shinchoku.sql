INSERT INTO miyagi_archive_shinchoku.daily_shinchoku
SELECT
	NOW() AS shinchoku_date,
	1 AS categoryid,
	holderid,
	COUNT(holderid) AS content_num,
	COUNT(IF(md_copyright!=0 AND kiso_editflag=0 AND kihon_editflag=0, 1, NULL)) AS copyright_num,
	COUNT(IF(md_imageright!=0 AND kiso_editflag=0 AND kihon_editflag=0, 1, NULL)) AS imageright_num,
	COUNT(IF(md_copyright!=0 AND md_imageright!=0 AND kiso_editflag=0 AND kihon_editflag=0, 1, NULL)) AS complete_num
FROM miyagi_archive_ken.content
WHERE holderid>=121000
GROUP BY holderid
UNION ALL
SELECT
	NOW() AS shinchoku_date,
	2 AS categoryid,
	holderid,
	COUNT(holderid) AS content_num,
	COUNT(IF(md_copyright!=0 AND kiso_editflag=0 AND kihon_editflag=0, 1, NULL)) AS copyright_num,
	COUNT(IF(md_imageright!=0 AND kiso_editflag=0 AND kihon_editflag=0, 1, NULL)) AS imageright_num,
	COUNT(IF(md_copyright!=0 AND md_imageright!=0 AND kiso_editflag=0 AND kihon_editflag=0, 1, NULL)) AS complete_num
FROM miyagi_archive_shichouson.content
WHERE holderid<990
GROUP BY holderid
UNION ALL
SELECT
	NOW() AS shinchoku_date,
	3 AS categoryid,
	municipality_id AS holderid,
	COUNT(municipality_id) AS content_num,
	COUNT(IF(copyright!=0, 1, NULL)) AS copyright_num,
	COUNT(IF(imageright!=0, 1, NULL)) AS imageright_num,
	COUNT(IF(copyright!=0 AND imageright!=0, 1, NULL)) AS complete_num
FROM miyagi_archive_shinchoku.digital_team_shinchoku
GROUP BY municipality_id;
