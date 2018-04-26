<?php
namespace App\Presenters;

class GuidePreServicePresenter
{
	public $serviceName  ;
	public $nameTitle_EN ;
	public $nameTitle_CH ;
	public function getServiceTitle($service)
	{	
		$this->dataConvert($service);
		return $this->nameTitle_CH;

	}
	public function getPreServiceName($service)
	{
		$this->dataConvert($service);
		return $this->serviceName;
	}

	public function dataConvert($service)
	{
		switch($service){
			case 'AtSe':
				$serviceName  = 'Airport_Shuttle';
				$nameTitle_EN = 'Airport Shuttle';
				$nameTitle_CH = '機場接送';
			break;
			case 'SdPe':
				$serviceName = 'SimCard_Purchase';
				$nameTitle_EN = 'SimCard Purchase';
				$nameTitle_CH = '號碼代辦';
			break;
			case 'CrCr':
				$serviceName = 'Car_Charter';
				$nameTitle_EN = 'Car Charter';
				$nameTitle_CH = '汽車伴遊';

			break;
			case 'MeCr':
				$serviceName = 'MotorBike_Charter';
				$nameTitle_EN = 'MotorBike Charter';
				$nameTitle_CH = '機車伴遊';

			break;
			default:
				$serviceName = '';
		}

		$this->serviceName  = $serviceName ;
		$this->nameTitle_EN = $nameTitle_EN;
		$this->nameTitle_CH = $nameTitle_CH;
	}
}
?>