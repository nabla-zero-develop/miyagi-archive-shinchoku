�i���Ǘ��V�X�e��
========

�g�p���C�u����
--------
shinchoku/css/jquery-ui ... jQuery UI v1.10.4  
shinchoku/css/tablesorter ... tablesorter v2.0.5  
shinchoku/js/jquery-1.11.1.min.js ... jQuery v1.11.1  
shinchoku/js/jquery-ui-1.10.4.custom.min.js ... jQuery UI v1.10.4  
shinchoku/js/jquery.ui.datepicker-ja.min.js ... Struts2-jQuery(datepicker���{�ꉻ�p)  
shinchoku/js/jquery.tablesorter.min.js ... tablesorter v2.0.5  

������s�v���O����
--------
### ���s���@
`mysql -u <���[�U��> -p<�p�X���[�h> < <sql�t�@�C�������݂���f�B���N�g��>/shinchoku.sql`

### ������s�̐ݒ���@
production_program/shinchoku.sql��C�ӂ̃f�B���N�g���ɃR�s�[���Acron�ɏ�L���s���@��ǋL����

Web�ݒ���@
--------
### �ݒ���@
1. shinchoku�t�H���_�����J�ꏊ�ɃR�s�[����B
2. shinchoku�t�H���_�̒�����`_config.php`�Ƃ������O�̃t�@�C�����쐬����B
3. �V�K�쐬����`_config.php`�ɉ��L�̓��e���L�q����B���ɍ��킹�Đݒ肷��B

<?php  
$db["host"] = "MySQL�����삵�Ă���T�[�o�ւ�URL";  
$db["user"] = "���[�U��";  
$db["password"] = "�p�X���[�h";  
