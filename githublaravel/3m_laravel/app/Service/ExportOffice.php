<?php

namespace App\Service;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Class ExportOffice
 * @package App\Service
 * php office 导出
 */
class ExportOffice
{

    public function exportList($header,$data,$fileName)
    {
//        $data = [
//            ['adata1', 'adata2'],
//            ['bdata1', 'bdata2']
//        ];
//        $header = ['tit1','tit2'];
//        $fileName = 'test';
        $this->export($header, $data, $fileName, storage_path().'/');//导出
//        dd($this->read('c:/Users/liu/Downloads/test.xlsx')->toArray());//导入
        return 'result';
    }



    /**
     * 导出excel表并保存到服务器
     * @param array $title 标题行名称
     * @param array $data 导出数据
     * @param string $file_name 文件名
     * @param string $save_path 保存路径
     * @param int $options 下载或保存
     * @return string   返回文件全路径
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export($title = array(), $data = array(), $file_name = '', $save_path = './', $options = 1)
    {
        //实例化类
        $spreadsheet = new Spreadsheet();

        //横向单元格标识
        $cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');
        //设置sheet名称   $spreadsheet->getActiveSheet(0)  --- 获取工作簿0
        $spreadsheet->getActiveSheet(0)->setTitle('sheet1');
        //设置纵向单元格标识
        $_row = 1;
        if ($title) {
            $_cnt = count($title);
            $spreadsheet->getActiveSheet(0)->mergeCells('A' . $_row . ':' . $cellName[$_cnt - 1] . $_row);   //合并单元格
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $_row, $file_name.".". date('Y-m-d H:i:s'));//设置合并后的单元格内容
            $_row++;
            $i = 0;
            foreach ($title AS $v) {   //设置列标题
                $spreadsheet->setActiveSheetIndex(0)->setCellValue($cellName[$i] . $_row, $v);
                $i++;
            }
            $_row++;
        }
        //填写数据
        if ($data) {
            $i = 0;
            foreach ($data AS $_v) {
                $j = 0;
                foreach ($_v AS $_cell) {
                    $spreadsheet->getActiveSheet(0)->setCellValue($cellName[$j] . ($i + $_row), $_cell);
                    $j++;
                }
                $i++;
            }
        }
        //文件名处理
        if (!$file_name) {
            $file_name = uniqid(time(), TRUE);
        }
        $writer = new Xlsx($spreadsheet);
        if ($options == 1) {   //网页下载
            header('pragma:public');
            header("Content-Disposition:attachment;filename = $file_name.xlsx");
            $writer->save('php://output');
        } else if ($options == 0) {//存储到后台
            $file_name = iconv("utf-8", "gb2312", $file_name);   //转码
            $save_path = $save_path . $file_name . '.xlsx';
            $writer->save($save_path);
            return $file_name . '.xlsx';
        } else if ($options == 2) { //网页下载 + 存储到后台
            header('pragma:public');
            header("Content-Disposition:attachment;filename = $file_name.xlsx");
            $writer->save('php://output');
            $file_name = iconv("utf-8", "gb2312", $file_name);   //转码
            $save_path = $save_path . $file_name . '.xlsx';
            $writer->save($save_path);
        }
        //删除清空：
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        exit;
    }



    /**
     * 读取excel里面的内容保存为数组
     * @param string $file_path
     * @param array $read_column
     * @return array|\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function read($file_path = '/', $read_column = array())
    {
        $reader = IOFactory::createReader('Xlsx');

        $reader->setReadDataOnly(TRUE);

        //载入excel表格
        $spreadsheet = $reader->load($file_path);

        // 读取第一個工作表
        $sheet = $spreadsheet->getSheet(0);

        // 取得总行数
        $highest_row = $sheet->getHighestRow();

        // 取得总列数
        $highest_column = $sheet->getHighestColumn();
//        dd($highest_row, $highest_column, $sheet->toArray());

        return $sheet;
    }




    /**
     * 模板导出csv
     * 教师通知
     * @param string $expTitle 导出文件名.例：财务20201010
     * @param string $template_name 模板名称：模板在本地的名称，如template.xls
     * @param string $content 内容 标题/r/n内容。其中使用/r/n表示换行
     * @param array $datas 数据 array("张三","18360608080","upload/img/ceshi.jpg","2020-10-10")
     * @param int $start 开始循环载入数据的行数
     * @param array $imgArr 数据中的图片占用位 例子：array(2)，数据中，图片为第2位
     * @return array
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function getCsv($expTitle = "", $datas = [], $start = 1,$timeString,$pv,$uv)
    {

        //模板名称
        !empty($expTitle) ? $expTitle : $expTitle = '数据统计'.date('YmdHis',time()).rand(1000,9999).'.xls';
        header('pragma:public');
        header("Content-Disposition:attachment;filename = $expTitle");
        $templatefilename = "./template2.xlsx";
        $objPHPExcel = IOFactory::load($templatefilename);
        //头部内容
        $objPHPExcel->getActiveSheet()->setCellValue('A1', '3m工业选型京东小程序');
        $objPHPExcel->getActiveSheet()->setCellValue('B2', '3m工业选型京东小程序');
        $objPHPExcel->getActiveSheet()->setCellValue('B3', $timeString);
        //默认数据 uv/pv 比值
        $puSpecificValue = ((int) $pv / (int) $uv);
        $puSpecificValue = number_format($puSpecificValue,2);
        $objPHPExcel->getActiveSheet()->setCellValue('B7', $pv);
        $objPHPExcel->getActiveSheet()->setCellValue('C7', $uv);
        $objPHPExcel->getActiveSheet()->setCellValue('D7',(int) $puSpecificValue);
        //总数据统计
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->setActiveSheetIndex(1)->setCellValue('A1', "物质类($timeString)");

        $objPHPExcel->setActiveSheetIndex(2)->setCellValue('A1', "产品选择($timeString)");

        //物质数据
        $matterList = $datas['matter'];
        //产品数据
        $productList = $datas['product'];

        $styleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ];

        $matterList = $this->toIndexArr($matterList);
        foreach ($matterList as $k => $v) {
            $num = $k +3;

            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('A'.$num, ' '.$v['name']);
            $objPHPExcel->getActiveSheet(1)->getStyle('A'.$num)->applyFromArray($styleArray);

            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('B'.$num, $v['pv']);
            $objPHPExcel->getActiveSheet(1)->getStyle('B'.$num)->applyFromArray($styleArray);

            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('C'.$num, $v['uv']);
            $objPHPExcel->getActiveSheet(1)->getStyle('C'.$num)->applyFromArray($styleArray);

            if($v['pv']==0 || $v['uv']==0){
                $puSpecificValue = 0.00;
            } else{
                $puSpecificValue = ((int) $v['pv'] / (int) $v['uv']);
                $puSpecificValue = number_format($puSpecificValue,2);
            }
            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('D'.$num, $puSpecificValue);
            $objPHPExcel->getActiveSheet(1)->getStyle('D'.$num)->applyFromArray($styleArray);

        }



        $productList = $this->toIndexArr($productList);

        foreach ($productList as $key => $item) {

            $num = $key +3;

            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('A'.$num, ' '.$item['name']);
            $objPHPExcel->getActiveSheet(2)->getStyle('A'.$num)->applyFromArray($styleArray);

            //选择
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('B'.$num, $item['choose_pv']);
            $objPHPExcel->getActiveSheet(2)->getStyle('B'.$num)->applyFromArray($styleArray);

            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('C'.$num, $item['choose_uv']);
            $objPHPExcel->getActiveSheet(2)->getStyle('C'.$num)->applyFromArray($styleArray);
            //比值
            if($item['choose_pv']==0 || $item['choose_uv']==0){
                $chooseSpecificValue = 0.00;
            } else{
                $chooseSpecificValue = ((int) $item['choose_pv'] / (int) $item['choose_uv']);
                $chooseSpecificValue = number_format($chooseSpecificValue,2);
            }
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('D'.$num, $chooseSpecificValue);
            $objPHPExcel->getActiveSheet(2)->getStyle('D'.$num)->applyFromArray($styleArray);


            //购买
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('E'.$num, $item['buy_pv']);
            $objPHPExcel->getActiveSheet(2)->getStyle('E'.$num)->applyFromArray($styleArray);

            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('F'.$num, $item['buy_uv']);
            $objPHPExcel->getActiveSheet(2)->getStyle('F'.$num)->applyFromArray($styleArray);

            //比值
            if($item['buy_pv']==0 || $item['buy_uv']==0){
                $buySpecificValue = 0.00;
            } else{
                $buySpecificValue = ((int) $item['buy_pv'] / (int) $item['buy_uv']);
                $buySpecificValue = number_format($buySpecificValue,2);
            }
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('G'.$num, $buySpecificValue);
            $objPHPExcel->getActiveSheet(2)->getStyle('G'.$num)->applyFromArray($styleArray);


            //购物车
            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('H'.$num, $item['addcart_pv']);
            $objPHPExcel->getActiveSheet(2)->getStyle('H'.$num)->applyFromArray($styleArray);

            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('I'.$num, $item['addcart_uv']);
            $objPHPExcel->getActiveSheet(2)->getStyle('I'.$num)->applyFromArray($styleArray);

            //购物车比值
            if($item['addcart_pv']==0 || $item['addcart_uv']==0){
                $cartSpecificValue = 0.00;
            } else{
                $cartSpecificValue = ((int) $item['addcart_pv'] / (int) $item['addcart_uv']);
                $cartSpecificValue = number_format($cartSpecificValue,2);
            }

            $objPHPExcel->setActiveSheetIndex(2)->setCellValue('J'.$num, $cartSpecificValue);
            $objPHPExcel->getActiveSheet(2)->getStyle('J'.$num)->applyFromArray($styleArray);

        }

        $objWriter = IOFactory::createWriter($objPHPExcel, 'Xls');
        //新目录
        $path = "./xxls/".$expTitle;
        $objWriter->save($path);
        $objWriter->save( 'php://output');
        $objPHPExcel->disconnectWorksheets();
        unset($spreadsheet);
        exit;
    }


    /**
     * @param array $array
     * @return array
     * 数据拆分
     */
    public function SplitData($array =[])
    {
        $return = [];
        $matter = [];
        $product = [];
        foreach ($array as $value)
        {
            if($value['object_type']=='product'){
                array_push($product,$value);
            }else{
                array_push($matter,$value);
            }
        }
        $return['product'] = $product;
        $return['matter'] = $matter;
        return $return;
    }




    /**
     *@param$arr
     *@returnarray
     *关联数组转成索引数组(一维)
     */
    public function toIndexArr($arr){
        $i=0;
        foreach($arr as $key=>$value){
            $newArr[$i]=$value;
            $i++;
        }
        return$newArr;
    }


}
