<?php
require "db.php";
class model extends db
{	
	function __construct()
	{
		parent::__construct();
		$this->table        = 'bfq_credit_apply';
		$this->memberTable  = 'bfq_member';
		$this->creditStatus = array(1=>'预审批，请及时处理',2=>'审批拒绝',3=>'预审批通过',4=>'通知拒绝',5=>'付款中(通知通过)',6=>'已放款',7=>'已结算',8=>'已关闭',9=>'拒绝放款');
	}

	public function excel()
	{
		$sql = "SELECT `a`.*, `b`.* FROM (`bjw_account_company` as a) JOIN `bjw_company_info` as b ON `b`.`itemid` = `a`.`companyId` WHERE a.parentId = 0 AND 1=1 AND a.sourceId = 0 ORDER BY `a`.`userid` desc";
		$this->query($sql);
		$list['list'] = $this->getArray();
		return $list;
	}

	public function getList($data = array())
	{

		$condition = $this->_filter($data);

		$group = '';
		if(isset($data['group']) && !empty($data['group'])){

			$group = "GROUP BY ".$data['group']." DESC";

		}

		$pageIndex = isset($data['pageIndex']) && intval($data['pageIndex']) ? intval($data['pageIndex']) : 1;
		$pageSize  = isset($data['pageSize']) && intval($data['pageSize']) ? intval($data['pageSize']) : 10;

		$m = $pageIndex - 1;
		$n = $pageSize * $m;
		$limit = " LIMIT $m, $n";

		$field = "COUNT(itemid) as totalNum, applyStatus";
		$sql = "SELECT $field FROM $this->table a LEFT JOIN $this->memberTable b ON a.userid = b.userid $condition";		
		$this->query($sql);
		$list = $this->getRow();

		$field = "*";
		$sql = "SELECT $field FROM $this->table a LEFT JOIN $this->memberTable b ON a.userid = b.userid $condition $group $limit ";
		$this->query($sql);
		$list['list'] = $this->getArray();
		$list['totalPage'] = ceil($list['totalNum']/$pageSize);
		$list['pageIndex'] = $pageIndex;
		$list['pageSize'] = $pageSize;
		return $list;
	}
	/**
	 * [getApprovalStatusList 获取某情况下状态数量]
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function getAllList($data)
	{
		$condition = $this->_filter($data);

		$group = '';
		if(isset($data['group']) && !empty($data['group'])){

			$group = "GROUP BY ".$data['group']." DESC";

		}

		$field = "*";
		if(isset($data['field']) && !empty($data['field'])){

			$field = $data['field'];

		}

		$sql = "SELECT $field FROM $this->table a LEFT JOIN $this->memberTable b ON a.userid = b.userid $condition $group";
		$this->query($sql);
		$list = $this->getArray();
		return $list;
	}

	public function getOne($data = '')
	{
		$condition = $this->_filter($data);

		$group = '';
		if(isset($data['group']) && !empty($data['group'])){

			$group = "GROUP BY ".$data['group']." DESC";

		}

		$field = "*";
		if(isset($data['field']) && !empty($data['field'])){

			$field = $data['field'];

		}

		$sql = "SELECT $field FROM $this->table a LEFT JOIN $this->memberTable b ON a.userid = b.userid $condition $group";
		// echo $sql;
		$this->query($sql);
		return $this->getRow();
	}

	private function _filter($data)
	{
		extract($data);
		$condition = "WHERE 1";

		if(isset($applyStartTime) && !empty($applyStartTime)){
			$condition .= " AND a.applyTime >= '{$applyStartTime}' ";
		}

		if(isset($applyEndTime) && !empty($applyEndTime)){
			$condition .= " AND a.applyTime <= '{$applyEndTime}' ";
		}

		if(isset($noticeiStartTime) && !empty($noticeiStartTime)){
			$condition .= " AND a.notificationAuditTime >= '{$noticeiStartTime}' ";
		}

		if(isset($noticeiEndTime) && !empty($noticeiEndTime)){
			$condition .= " AND a.notificationAuditTime <= '{$noticeiEndTime}' ";
		}

		if(isset($loanReviewStartTime) && !empty($loanReviewStartTime)){
			$condition .= " AND a.loanReviewTime >= '{$loanReviewStartTime}' ";
		}

		if(isset($loanReviewEndTime) && !empty($loanReviewEndTime)){
			$condition .= " AND a.loanReviewTime <= '{$loanReviewEndTime}' ";
		}

		// 3种时间
		if(isset($approvalStartTime) && !empty($approvalStartTime) && isset($approvalEndTime) && !empty($approvalEndTime)){

			$condition .= " AND ((a.applyTime between '{$approvalStartTime}' AND '{$approvalEndTime}') or ";
			$condition .= " (a.notificationAuditTime between '{$approvalStartTime}' AND '{$approvalEndTime}') or ";
			$condition .= " (a.loanReviewTime between '{$approvalStartTime}' AND '{$approvalEndTime}')) ";


		}
		
		return $condition;

	}


	public function getApplyStatusList($data)
	{
		$data['field'] = "COUNT(itemid) as totalNum, applyStatus";
		$list =  $this->getAllList($data);
		$temp = array();
		foreach ($list as $k => $v) {
			$temp[$v['applyStatus']] = $v['totalNum'];
		}
		return $temp;
	}

}