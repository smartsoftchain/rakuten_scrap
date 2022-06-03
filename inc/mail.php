<?php
/*
 * ���[�����M����
 *
 * UTF8�܂���JIS�R�[�h�ő��M����
 */

/*sendmail($from, $email, $title, $body, array(), $from, $from_name);
 * ���[�����M�����A�{�����t�@�C������
 *
 * �������ݏ����A�����T�u�W�F�N�g�ɑΉ�
 */
function sendmail_file($mail_from, $mail_to, $mail_subject, $body_file, $reply=null, $from_name=null)
{
	$body = file_get_contents($body_file);
	return sendmail($mail_from, $mail_to, $mail_subject, $body, $reply, $from_name);
}
/*
 * ���[�����M�����A�{����ϐ�����
 *
 * �������ݏ����A�����T�u�W�F�N�g�ɑΉ�
 */
function sendmail($mail_from, $mail_to, $mail_subject, $body, $reply=null, $from_name=null)
{
	
	$mail_from = trim($mail_from);
	$mail_to = trim($mail_to);
	$mail_subject = trim($mail_subject);
	$body = trim($body);
	$reply = trim($reply);
	$from_name = trim($from_name);
	
	$MAIL_ENCODING = "JIS";
	$SCRIPT_ENCODING = "utf-8";
	// ���M�����̍쐬
	if ($from_name) {
		$header = "From: ";
		if ($MAIL_ENCODING == "UTF8") {
			$header .= '=?utf-8?B?';
		} else {
			$header .= '=?iso-2022-jp?B?';
		}
		$header .= base64_encode(mb_convert_encoding($from_name, $MAIL_ENCODING, $SCRIPT_ENCODING)) .
			 '?= <' . $mail_from . ">";
	} else {
		$header = "From: " . $mail_from;
	}
	// �����R�[�h�̎w��
	if ($MAIL_ENCODING == "UTF8") {
		$header .= "\nContent-Type: text/plain;\n\tformat=flowed;\n\tcharset=\"utf-8\";\n\treply-type=original";
	} else {
		$header .= "\nContent-Type: text/plain;\n\tcharset=\"iso-2022-jp\"\nContent-Transfer-Encoding: 7bit";
	}
	// �ԐM��w��
	if ($reply) {
		$header .= "\nReply-to: <" . $reply . ">\nReturn-Path: <" . $reply . ">";
	}
	$parameter="-f ".$reply;
	// �����̕ϊ�
	$subject_str = '';
	while ($mail_subject) {
		if ($subject_str) {
			$subject_str .= "\n\t";
		}
		if (mb_strlen($mail_subject, $SCRIPT_ENCODING) < 20) {
			if ($MAIL_ENCODING == "UTF8") {
				$subject_str .= '=?utf-8?B?';
			} else {
				$subject_str .= '=?iso-2022-jp?B?';
			}
			$subject_str .= base64_encode(mb_convert_encoding($mail_subject, $MAIL_ENCODING, $SCRIPT_ENCODING)) . '?=';
			$mail_subject = '';
		} else {
			if ($MAIL_ENCODING == "UTF8") {
				$subject_str .= '=?utf-8?B?';
			} else {
				$subject_str .= '=?iso-2022-jp?B?';
			}
			$subject_str .= base64_encode(mb_convert_encoding(mb_substr($mail_subject, 0, 20, $SCRIPT_ENCODING), $MAIL_ENCODING, $SCRIPT_ENCODING)) . '?=';
			$mail_subject = mb_substr($mail_subject, 20, mb_strlen($mail_subject, $SCRIPT_ENCODING) - 20, $SCRIPT_ENCODING);
		}
	}
	// ���M����
	return mail($mail_to, $subject_str, mb_convert_encoding($body, $MAIL_ENCODING, $SCRIPT_ENCODING), $header);
}
?>
