<?php

/**
 * @Created by          : Waris Agung Widodo (ido.alit@gmail.com)
 * @Date                : 09/11/20 23.24
 * @File name           : VisitorController.php
 */
class VisitorController extends Controller
{
     protected $sysconf;

     /**
      * @var mysqli
      */
     protected $db;


     function __construct($sysconf, $obj_db)
     {
          $this->sysconf = $sysconf;
          $this->db = $obj_db;
     }

     function getSummary()
     {
          parent::withJson([
               'data' => [
                    'total' => $this->getTotal(),
               ]
          ]);
     }

     private function getTotal()
     {
          $query = $this->db->query("SELECT COUNT(visitor_id) FROM visitor_count");
          return ($query->fetch_row())[0];
     }
     public function getChart()
     {
          // months array
          $months['01'] = __('Jan');
          $months['02'] = __('Feb');
          $months['03'] = __('Mar');
          $months['04'] = __('Apr');
          $months['05'] = __('May');
          $months['06'] = __('Jun');
          $months['07'] = __('Jul');
          $months['08'] = __('Aug');
          $months['09'] = __('Sep');
          $months['10'] = __('Oct');
          $months['11'] = __('Nov');
          $months['12'] = __('Dec');

          $visitor_data = array();
          // year
          $selected_year = date('Y');
          // month
          $selected_month = date('m');

          // for each day in the month
          for ($i = 1; $i <=  date('t', strtotime($selected_year . '-' . $selected_month)); $i++) {
               $date = str_pad($i, 2, '0', STR_PAD_LEFT);
               $xAxis[$date] = $date;
               $data['member'][$date] = 0;
               $data['visitor'][$date] = 0;
          }

          // query visitor data to database
          // $_visitor_q = $this->db->query("SELECT MAX(SUBSTRING(`checkin_date`, 9, 2)) AS `mdate`, COUNT(visitor_id) AS `vtotal` FROM `visitor_count` WHERE `checkin_date` LIKE '$selected_year-$selected_month%' GROUP BY DATE(`checkin_date`)");
          // while ($_visitor_d = $_visitor_q->fetch_row()) {
          //      $date = (int)preg_replace('@^0+@i', '', $_visitor_d[0]);
          //      $visitor_data[$date] = '<div class="data"><a class="notAJAX openPopUp" width="800" height="600" title="' . __('Visitor Report by Day') . '" href="' . AWB . 'modules/reporting/customs/visitor_list.php?reportView=true&startDate=' . $selected_year . '-' . $selected_month . '-' . $date . '&untilDate=' . $selected_year . '-' . $selected_month . '-' . $date . '">' . ($_visitor_d[1] ? $_visitor_d[1] : '0') . '</a></div>';
          //      $data['visitor'][$_visitor_d[0]] = $_visitor_d[1];
          // }

          $_visitor_q = $this->db->query("SELECT MAX(SUBSTRING(`checkin_date`, 9, 2)) AS `mdate`, COUNT(visitor_id) AS `vtotal` FROM `visitor_count` WHERE member_id IS NULL AND `checkin_date` LIKE '$selected_year-$selected_month%' GROUP BY DATE(`checkin_date`)");
          while ($_visitor_d = $_visitor_q->fetch_row()) {
               $date = (int)preg_replace('@^0+@i', '', $_visitor_d[0]);
               $visitor_data[$date] = '<div class="data"><a class="notAJAX openPopUp" width="800" height="600" title="' . __('Visitor Report by Day') . '" href="' . AWB . 'modules/reporting/customs/visitor_list.php?reportView=true&startDate=' . $selected_year . '-' . $selected_month . '-' . $date . '&untilDate=' . $selected_year . '-' . $selected_month . '-' . $date . '">' . ($_visitor_d[1] ? $_visitor_d[1] : '0') . '</a></div>';
               $data['visitor'][$_visitor_d[0]] = $_visitor_d[1];
          }

          $_visitor_h = $this->db->query("SELECT MAX(SUBSTRING(`checkin_date`, 9, 2)) AS `mdate`, COUNT(visitor_id) AS `vtotal` FROM `visitor_count` WHERE member_id IS NOT NULL AND `checkin_date` LIKE '$selected_year-$selected_month%' GROUP BY DATE(`checkin_date`)");
          while ($_visitor_i = $_visitor_h->fetch_row()) {
               $date = (int)preg_replace('@^0+@i', '', $_visitor_i[0]);
               $visitor_data[$date] = '<div class="data"><a class="notAJAX openPopUp" width="800" height="600" title="' . __('Visitor Report by Day') . '" href="' . AWB . 'modules/reporting/customs/visitor_list.php?reportView=true&startDate=' . $selected_year . '-' . $selected_month . '-' . $date . '&untilDate=' . $selected_year . '-' . $selected_month . '-' . $date . '">' . ($_visitor_i[1] ? $_visitor_i[1] : '0') . '</a></div>';
               $data['member'][$_visitor_i[0]] = $_visitor_i[1];
          }

     

          unset($_SESSION['chart']);
          $chart['xAxis'] = $xAxis;
          $chart['data'] =  $data;
          $chart['title'] =  str_replace(array('{selectedYear}', '{selectedMonth}'), array($selected_year, $months[$selected_month]), __('Visitor Report for <strong>{selectedMonth}, {selectedYear}</strong>'));
          $_SESSION['chart'] = $chart;

          parent::withJson([
               $chart
          ]);
     }
}
