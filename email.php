<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Emailsender {
		function __construct() {
			$this->CI =& get_instance();
		}
		
		
		function singleemail($reg_id = ''){
			if(!empty($reg_id)){
				$param= array();
				$param['reg_id'] = $reg_id;
				$this->CI->load->model('admin/m_enquiry');
				$list = $this->CI->m_enquiry->fetch($param);
				if(!empty($list) && is_array($list)){
					$data = array();
					$data['name'] = $list[0]['reg_fname'].' '.$list[0]['reg_lname'];
					$id = $list[0]['reg_id'].'-'.$list[0]['reg_mobile'];
					$data['link'] = base_url().'user/profile/create?id='.base64_encode($id);
					$email  = $list[0]['reg_email'];
					$subject = 'Matchme Sign Up Mail';
					$content = $this->CI->load->view('mailTemplate/signup/signup',$data ,TRUE);
					//echo $content; exit;
					$attachment = array();
					$attachment['Membership_Agreement'] = base_url().'uploads/signup/Membership_Agreement.pdf';
					$attachment['Premium_Membership'] = base_url().'uploads/signup/Premium_Membership.pdf';
					$attachment['Non_membership_agreement'] = base_url().'uploads/signup/Non_membership_agreement.pdf';
					//echo $content; exit;
					$return = $this->senderAttachment($email,$subject,$content,$attachment);
					if(!empty($return)){
						$this->save($email,$subject,$return);
						$param['flag'] = 2;
					}else{
						$this->save($email,$subject,$return);
						$param['flag'] = 2;
					}
					return $return;
				}else{
					return -3;
				}
			}else{
				return -4;
			}
		}


		function profileemail($reg_id = ''){
			if(!empty($reg_id)){
				$param= array();
				$param['reg_id'] = $reg_id;
				$this->CI->load->model('admin/m_enquiry');
				$list = $this->CI->m_enquiry->fetch($param);
				if(!empty($list) && is_array($list)){
					$data = array();
					$data['name'] = $list[0]['reg_fname'].' '.$list[0]['reg_lname'];
					$id = $list[0]['reg_id'].'-'.$list[0]['reg_mobile'];
					$data['link'] = base_url().'user/profile/create?id='.base64_encode($id);
					$email  = $list[0]['reg_email'];
					$subject = 'Matchme Profile created successfully';

					$this->CI->load->model('admin/m_profile');
					$mlist = $this->CI->m_profile->fetch($param);
					$data['list'] = $mlist[0];
					$alt_email  = !empty($mlist[0]['alt_email'])?$mlist[0]['alt_email']:'';
					$content = $this->CI->load->view('mailTemplate/signup/thankyou',$data ,TRUE);
					//echo $content; exit;
					$return = $this->sender($email,$subject,$content,$alt_email);
					if(!empty($return)){
						$this->save($email,$subject,$return);
						$param['flag'] = 2;
					}else{
						$this->save($email,$subject,$return);
						$param['flag'] = 2;
					}
					return $return;
				}else{
					return -3;
				}
			}else{
				return -4;
			}
		}

		
		function profileAttachMail($reg_id = '', $profile_id = '',$email = '',$fullname = '',$matchRegID){
			if(!empty($reg_id) && !empty($profile_id) && !empty($email) && !empty($fullname)){
				$param= array();
				$param['reg_id'] = $reg_id;
				$param['profile_id'] = $profile_id;
				$this->CI->load->model('admin/m_profile');
				$list = $this->CI->m_profile->fetch($param);
				if(!empty($list) && is_array($list)){

					$mparam= array();
					$mparam['reg_id'] = $matchRegID;
					$mlist = $this->CI->m_profile->fetch($mparam);
					if(!empty($mlist) && is_array($mlist)){
						$alt_email  = !empty($mlist[0]['alt_email'])?$mlist[0]['alt_email']:'';
					}else{
						$alt_email = '';
					}


					$data = array();
					$data['name'] = $fullname;
					$subject = 'Matchme Profile';

					$content = $this->CI->load->view('mailTemplate/signup/profile',$data ,TRUE);
					//echo $content; exit;
					$attachment = array();
					$attachment['profile_image1'] = $list[0]['profile_image1'];
					$attachment['profile_image2'] = $list[0]['profile_image2'];
					$attachment['profile'] = !empty($this->_pdfgenerate($list[0]))?$this->_pdfgenerate($list[0]):'';

					$return = $this->senderAttachment($email,$subject,$content,$attachment,$alt_email);
					if(!empty($return)){
						$this->save($email,$subject,$return);
						$param['flag'] = 2;
					}else{
						$this->save($email,$subject,$return);
						$param['flag'] = 2;
					}
					return $return;
				}else{
					return -3;
				}
			}else{
				return -4;
			}
		}

		function meetingemail($reg_id = '',$meet_id = ''){
			if(!empty($reg_id) && !empty($meet_id)){
				$param= array();
				$param['reg_id'] = $reg_id;
				$this->CI->load->model('admin/m_enquiry');
				$list = $this->CI->m_enquiry->fetch($param);
				if(!empty($list) && is_array($list)){
					$param= array();
					$param['meet_id'] = $meet_id;
					$this->CI->load->model('admin/m_meeting');
					$mlist = $this->CI->m_meeting->fetch($param);
					if(!empty($mlist) && is_array($mlist)){
						$data = array();
						$data['name'] = $mlist[0]['meet_name'];
						$data['meet_date'] = date('d/m/Y',strtotime($mlist[0]['meet_date']));
						$data['meet_time'] = $mlist[0]['meet_time'];
						$data['meet_time_format'] = $mlist[0]['meet_time_format'];
						$email  = $list[0]['reg_email'];
						$subject = 'Matchme Meeting Mail';
						$content = $this->CI->load->view('mailTemplate/signup/meeting-mailer',$data ,TRUE);
						
						$return = $this->sender($email,$subject,$content);
						if(!empty($return)){
							$this->save($email,$subject,$return);
							$param['flag'] = 2;
						}else{
							$this->save($email,$subject,$return);
							$param['flag'] = 2;
						}
						return $return;
					}else{
						return -2;
					}
				}else{
					return -3;
				}
			}else{
				return -4;
			}
		}

		function invoiceMail($invo_id){
			if(!empty($invo_id)){
				$data = array();
				$param= array();
				$param['invo_id'] = $invo_id;
				$this->CI->load->model('admin/m_invoice');
				$list = $this->CI->m_invoice->fetch($param);
				if(!empty($list) && is_array($list)){
					$data['list'] = $list[0];
					$subject = 'Matchme Invoice';
					$email = $list[0]['invo_email'];
					$content = $this->CI->load->view('mailTemplate/signup/invoice',$data ,TRUE);
					//echo $content; exit;
					$attachment = array();
					$attachment['invoice'] = !empty($this->_invoicegenerate($list[0]))?$this->_invoicegenerate($list[0]):'';

					$return = $this->senderAttachment($email,$subject,$content,$attachment);
					return $return;
				}else{
					return -3;
				}
			}else{
				return -4;
			}
		}

		function _invoicegenerate($data){
			if(!empty($data) && is_array($data)){
				$this->CI->load->library('html2pdf');
				//Set folder to save PDF to
				$this->CI->html2pdf->folder('./uploads/pdfs/');
				//Set the filename to save/download as
				$file_name = date('ymd').$data['invo_id'].'.pdf';
				$this->CI->html2pdf->filename($file_name);
				//Set the paper defaults
				$this->CI->html2pdf->paper('a4', 'portrait');
				//Load html view
				
				$page =  $this->CI->load->view('admin/invoice/pdf/invoice', $data, true);
				
				//print_r($page);die;
				$this->CI->html2pdf->html($page);
				
				if($this->CI->html2pdf->create('save')) {
					$form_pdf = base_url().strstr($this->CI->html2pdf->create('save'),"uploads");

					return $form_pdf;
				}else{
					return -2;
				}
			}else{
				return -3;
			}	
		}

		
		function _pdfgenerate($data){
			if(!empty($data) && is_array($data)){
				$this->CI->load->library('html2pdf');
				//Set folder to save PDF to
				$this->CI->html2pdf->folder('./uploads/pdfs/');
				//Set the filename to save/download as
				$file_name = date('ymd').$data['profile_id'].'.pdf';
				$this->CI->html2pdf->filename($file_name);
				//Set the paper defaults
				$this->CI->html2pdf->paper('a4', 'portrait');
				//Load html view
				
				$page =  $this->CI->load->view('admin/profile/print/download', $data, true);
				
				//print_r($page);die;
				$this->CI->html2pdf->html($page);
				
				if($this->CI->html2pdf->create('save')) {
					$form_pdf = base_url().strstr($this->CI->html2pdf->create('save'),"uploads");

					return $form_pdf;
				}else{
					return -2;
				}
			}else{
				return -3;
			}	
		}

		function save($email,$subject,$return){
			$temp = array();
			$temp['log_email'] = $email;	
			$temp['log_subject'] = 	$subject;
			$temp['log_status'] = $return;
			$temp['log_date'] = date('Y-m-d H:m:s');
			$this->CI->load->model('m_common');
			$this->CI->m_common->inset_mail_logs($temp);
			return $return;
		}
		
		function sender($to_email,$subject,$content,$alt_email=''){
			if(!empty($to_email) && !empty($subject) && !empty($content)){
				$config = Array(
				    'protocol' => 'ssl',
				    'smtp_host' => 'tls://smtp.gmail.com',
				    'smtp_port' => 465,
				    'smtp_user' => 'matchme1006@gmail.com',
				    'smtp_pass' => 'mk$2HytlOp',
				    'mailtype'  => 'html', 
				    'charset'   => 'iso-8859-1'
				);
				
			  $this->CI->load->library('email',$config);
	          $this->CI->email->set_newline("\r\n");
	          $this->CI->email->from('admin@matchme.co.in', 'Matchme');
			  $this->CI->email->reply_to( 'admin@matchme.co.in', 'Matchme' );
	     	  $this->CI->email->cc('admin@matchme.co.in,'.$alt_email);
	          $this->CI->email->bcc('admin@matchme.co.in');
	          $this->CI->email->to($to_email);
	          $this->CI->email->subject($subject);
	          $this->CI->email->message($content);
	          $this->CI->email->set_mailtype('html');
          	// $result = $this->CI->email->send();
          	 //print_r( $result); exit;
               if($this->CI->email->send()){
					$return = 1;
				}else{
					$return = 0;
				}
				return $return;
			}else{
				return 0;
			}	

		}


		function senderAttachment($to_email,$subject,$content,$attachment,$alt_email=''){
			if(!empty($to_email) && !empty($subject) && !empty($content)){
				$config = Array(
				    'protocol' => 'ssl',
				    'smtp_host' => 'tls://smtp.gmail.com',
				    'smtp_port' => 465,
				    'smtp_user' => 'matchme1006@gmail.com',
				    'smtp_pass' => 'mk$2HytlOp',
				    'mailtype'  => 'html', 
				    'charset'   => 'iso-8859-1'
				);
				
			  $this->CI->load->library('email',$config);
	          $this->CI->email->set_newline("\r\n");
	          $this->CI->email->from('admin@matchme.co.in', 'Matchme');
			  $this->CI->email->reply_to( 'admin@matchme.co.in', 'Matchme' );
			  $list = array($alt_email, 'admin@matchme.co.in');
	     	  $this->CI->email->cc($list);
	          $this->CI->email->bcc('admin@matchme.co.in');
	          $this->CI->email->to($to_email);
	          $this->CI->email->subject($subject);
	          $this->CI->email->message($content);
	          $this->CI->email->set_mailtype('html');
	          if(!empty($attachment) && is_array($attachment)){
	          	foreach ($attachment as $key => $value) {
	          		if(!empty($value)){
	          			$this->CI->email->attach($value);
	          		}
	          	}
              	
          	  }
          	// $result = $this->CI->email->send();
          	 //print_r( $result); exit;
              if($this->CI->email->send()){
					$return = 1;
				}else{
					$return = 0;
				}
				return $return;
			}else{
				return 0;
			}	
		}
	}


