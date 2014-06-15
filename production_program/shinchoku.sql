INSERT into miyagi_archive_shinchoku.daily_shinchoku
SELECT
	now() AS shinchoku_date,
	1 AS categoryid,
	holderid,
	count(holderid) AS content_num,
	count(if(md_copyright!=0 and kiso_editflag=0 and kihon_editflag=0, 1, null)) as copyright_num,
	count(if(md_imageright!=0 and kiso_editflag=0 and kihon_editflag=0, 1, null)) as imageright_num,
	count(if(md_copyright!=0 and md_imageright!=0 and kiso_editflag=0 and kihon_editflag=0, 1, null)) as complete_num
FROM miyagi_archive_ken.content
WHERE holderid>=121000
GROUP BY holderid
UNION ALL
SELECT
	now() AS shinchoku_date,
	2 AS categoryid,
	holderid,
	count(holderid) AS content_num,
	count(if(md_copyright!=0 and kiso_editflag=0 and kihon_editflag=0, 1, null)) as copyright_num,
	count(if(md_imageright!=0 and kiso_editflag=0 and kihon_editflag=0, 1, null)) as imageright_num,
	count(if(md_copyright!=0 and md_imageright!=0 and kiso_editflag=0 and kihon_editflag=0, 1, null)) as complete_num
FROM miyagi_archive_shichouson.content
WHERE holderid<990
GROUP BY holderid;
