<?php

namespace smart\rbac\libs;

/**
 * $list = CategoryTable::newInstance()->getList($field,$condition);
 * $TreeHelper = new TreeHelper($list);
 * return $TreeHelper->format(0);
 *
 * 本类作为数据库操作的一个辅助函数，将类似于地区表，类别表等 按照parentId 进行排序，来展现在前端，效果类似于这样：
 * 可以显示在select下拉列表里,
 *
 * 一级分类
 * ----子分类1
 * ----子分类2
 * ----子分类3
 * 一级分类2
 * ----子分类1
 * ----子分类2
 *
 */
class TreeHelper
{
	private $treeData = array();
	private $arrayData = array();
	private $str = '';

	public function __construct($list,$columnName,$columnId,$columnParentId)
    {
        $this->arrayData = $list;
        $this->columnName = $columnName;
        $this->columnId = $columnId;
        $this->columnParentId = $columnParentId;
    }


	public function format($parentId=0,$i=0,$str='')
	{
        //
		$tmpList = self::group($this->arrayData,$this->columnParentId);

		if(!isset($tmpList[$parentId]))
		{
			return $this->treeData;
		}

		$nodeList = $tmpList[$parentId];

		if($parentId==0) {
			$str = '';
		} else {
			for($j=0;$j<$i;$j++)
			$str .= '----';
		}
		$i++;

		while($node = array_shift($nodeList) )
		{
			$node[$this->columnName] = $str."".$node[$this->columnName];
			$this->treeData[] = $node;
			
			$this->format($node[$this->columnId],$i);
			
		}
		return $this->treeData;

	}



    public static function group(array $data, $groupBy, $isTrimKey = true)
    {
        if (empty($data)) return null;
        $tmp = array();
        foreach ($data as $k => $v) {
            $index = $isTrimKey ? trim($v[$groupBy]) : $v[$groupBy];
            @$tmp[$index][] = $v;
        }

        return $tmp;
    }
}