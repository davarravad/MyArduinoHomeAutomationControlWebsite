<?php
/**
* Home Models
*
* UserApplePie
* @author David (DaVaR) Sargent <davar@userapplepie.com>
* @version 4.0.0
*/


namespace App\Models;

use App\System\Models,
    Libs\Database;

class Trailers extends Models {

    public function createYardCheck($user_id, $yard_id){
		$data = $this->db->insert(PREFIX.'yard_check', array('user_id' => $user_id, 'yard_id' => $yard_id));
		$new_yard_check_id = $this->db->lastInsertId('id');
		$count = count($data);
		if($count > 0){
		  return $new_yard_check_id;
		}else{
		  return false;
		}
    }

    public function addTrailerToYardCheck($count_id, $user_id, $trailer, $status, $notes, $yard_id){
		$data = $this->db->insert(PREFIX.'trailer_count', array('count_id' => $count_id, 'user_id' => $user_id, 'trailer' => $trailer, 'status' => $status, 'notes' => $notes, 'yard_id' => $yard_id));
		$count = count($data);
		if($count > 0){
		  return true;
		}else{
		  return false;
		}
    }

	public function updateTrailerToYardCheck($tc_id, $count_id, $u_id, $trailer, $status, $notes){
		$query = $this->db->update(PREFIX.'trailer_count', array('count_id' => $count_id, 'user_id' => $u_id, 'trailer' => $trailer, 'status' => $status, 'notes' => $notes), array('id' => $tc_id));
		$count = count($query);
		if($count > 0){
			return true;
		}else{
			return false;
		}
	}

    public function checkForTrailer($count_id, $trailer){
		$data = $this->db->select("
			SELECT
			    tc.*
			FROM
			  ".PREFIX."trailer_count tc
			WHERE
				tc.count_id = :count_id
            AND
                tc.trailer = :trailer
			ORDER BY
			  tc.id
			DESC
			",
			array(':count_id' => $count_id, ':trailer' => $trailer));
            $count = count($data);
            if($count > 0){
                return true;
            }else{
                return false;
            }
	}

	public function getCurrentYardCheck($count_id){
		$data = $this->db->select("
			SELECT
			    tc.*
			FROM
			  ".PREFIX."trailer_count tc
			WHERE
				tc.count_id = :count_id
			ORDER BY
			  tc.id
			DESC
			",
			array(':count_id' => $count_id));
		return $data;
	}

    public function getCurrentYardCheckInfo($count_id){
		$data = $this->db->select("
			SELECT
			    yc.*
			FROM
			  ".PREFIX."yard_check yc
			WHERE
				yc.id = :count_id
		    LIMIT 1
			",
			array(':count_id' => $count_id));
		return $data;
	}

	public function getCurrentYardCheckEdit($count_id, $tc_id){
		$data = $this->db->select("
			SELECT
			  *
			FROM
			  ".PREFIX."trailer_count
			WHERE
				count_id = :count_id
			AND
				id = :tc_id
			LIMIT 1
			",
			array(':count_id' => $count_id, ':tc_id' => $tc_id));
		return $data;
	}

    public function recentYardChecks($yard_id = null, $limit = null){
        if(filter_var($yard_id, FILTER_VALIDATE_INT)){
            $where_yard = "WHERE yc.yard_id = $yard_id";
        }else{
            $where_yard = "";
        }
        $data = $this->db->select("
            SELECT
              yc.*,
                (SELECT
                    COUNT(*)
                FROM
                    ".PREFIX."trailer_count tc
                WHERE
                    tc.count_id = yc.id
                ) as trailertotal,
                (SELECT
                    y.name
                FROM
                    ".PREFIX."yard y
                WHERE yc.yard_id = y.id
                limit 1) as location_name
            FROM
              ".PREFIX."yard_check yc
            $where_yard
            ORDER BY
              yc.id
            DESC
            $limit
            ");
		return $data;
	}

    public function getTotalCount($count_id, $count_type){
		$data = $this->db->select("
			SELECT
			  *
			FROM
			  ".PREFIX."trailer_count
			WHERE
				count_id = :count_id
            AND
                status = :count_type
			ORDER BY
			  id
			DESC
			",
			array(':count_id' => $count_id, ':count_type' => $count_type));
		return count($data);
	}

    public function sendYardCheckEmail($count_id, $email){
        $current_count = SELF::getCurrentYardCheck($count_id);

        $yard_check_info = SELF::getCurrentYardCheckInfo($count_id);

        $yard_info = SELF::getCurrentYard($yard_check_info[0]->yard_id);

        //EMAIL MESSAGE USING PHPMAILER
        $mail = new \Libs\PhpMailer\Mail();
        $mail->setFrom(SITEEMAIL, EMAIL_FROM_NAME);
        $mail->addAddress($email);
        $mail_subject = $yard_info[0]->name." Yard Check";
        $mail->subject($mail_subject);
        $body = "<b>".$yard_info[0]->name."</b> Yard Check<br/> ";
        $body .= $yard_info[0]->city.", ".$yard_info[0]->state."<br><br>";
        $body .= "
                <style>
                table {
                    border-collapse: collapse;
                }

                th, td {
                    text-align: left;
                    padding: 8px;
                }

                tr:nth-child(even){background-color: #f2f2f2}
                </style>
            ";

        $body .= "<table cellspacing='0' cellpadding='5' border='1'><tr>";
        $body .= "<th>Trailer #</th><th>Status</th><th>Notes</th>";
        $body ."</tr>";
            foreach($current_count as $row) {
                $body .= "<tr><td>$row->trailer</td><td>$row->status</td><td>$row->notes</td></tr>";
            }
        $body .= "</table>";
        $body .=" <br><Br> This E-Mail was sent from https://milantrailers.thedavar.net/ <br><br> Do Not Reply To This Email.";
        $mail->body($body);
        $mail->send();

        return true;
    }

    public function getCurrentYard($yard_id){
		$data = $this->db->select("
			SELECT
			    *
			FROM
			  ".PREFIX."yard
			WHERE
				id = :yard_id
			LIMIT 1
			",
			array(':yard_id' => $yard_id));
		return $data;
	}

    public function getYards($limit = null){
		$data = $this->db->select("
			SELECT
			    *
			FROM
			  ".PREFIX."yard
            ORDER BY
                state, city
            ASC
            $limit
			");
		return $data;
	}


    public function addNewYard($name = null, $address = null, $city = null, $state = null, $zip = null, $user_id){
		$data = $this->db->insert(PREFIX.'yard', array('name' => $name, 'address' => $address, 'city' => $city, 'state' => $state, 'zip' => $zip, 'user_id' => $user_id));
		$count = count($data);
		if($count > 0){
		  return true;
		}else{
		  return false;
		}
    }

	public function updateYard($yard_id, $name, $address, $city, $state, $zip, $user_id){
		$query = $this->db->update(PREFIX.'yard', array('name' => $name, 'address' => $address, 'city' => $city, 'state' => $state, 'zip' => $zip, 'user_id' => $user_id), array('id' => $yard_id));
		$count = count($query);
		if($count > 0){
			return true;
		}else{
			return false;
		}
	}

    public function checkYard($name){
		$data = $this->db->select("
			SELECT
			    *
			FROM
			  ".PREFIX."yard
			WHERE
				name = :name
			LIMIT 1
			",
			array(':name' => $name));
        $count = count($data);
        if($count > 0){
            return true;
        }else{
            return false;
        }
	}

    public function deleteYard($yard_id){
      $data1 = $this->db->delete(PREFIX.'yard', array('id' => $yard_id));
      $data2 = $this->db->delete(PREFIX.'yard_check', array('yard_id' => $yard_id), '999999');
      $data3 = $this->db->delete(PREFIX.'trailer_count', array('yard_id' => $yard_id), '999999');
      $data = $data1 + $data2 + $data3;
      $count = count($data);
      if($count > 0){
        return true;
      }else{
        return false;
      }
    }

    public function deleteYardCheck($where_id){
      $data1 = $this->db->delete(PREFIX.'yard_check', array('id' => $where_id));
      $data2 = $this->db->delete(PREFIX.'trailer_count', array('count_id' => $where_id), '999999');
      $data = $data1 + $data2;
      $count = count($data);
      if($count > 0){
        return true;
      }else{
        return false;
      }
    }

    public function deleteYardCheckTrailer($where_id){
      $data = $this->db->delete(PREFIX.'trailer_count', array('id' => $where_id));
      $count = count($data);
      if($count > 0){
        return true;
      }else{
        return false;
      }
    }

}
