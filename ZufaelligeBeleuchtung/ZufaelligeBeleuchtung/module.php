<?php
	class ZufaelligeBeleuchtung extends IPSModule {

		public function Create() {
			//Never delete this line!
			parent::Create();

			//Properties
			$this->RegisterPropertyString('Targets', '[]');
			$this->RegisterPropertyString('SwitchColors', '[{"Color":242944}, {"Color":16711680}, {"Color":16777215}]');
			$this->RegisterPropertyInteger('Interval', 1);
			
			//Variables
			$this->RegisterVariableBoolean('Active', $this->Translate('Christmas Mode'), '~Switch');
			$this->EnableAction('Active');
			$this->RegisterVariableInteger('ColorDisplay', 'Great Color!', '~HexColor');

			//Timer
			$this->RegisterTimer('ChangeTimer', 0, 'ZB_ChangeLight($_IPS[\'TARGET\']);');
		}

		public function Destroy(){
			//Never delete this line!
			parent::Destroy();

		}

		public function ApplyChanges() {
			//Never delete this line!
			parent::ApplyChanges();

		}

		public function RequestAction($Ident, $Value)
		{
			switch ($Ident) {
				case 'Active':
					$this->SetActive($Value);
					break;
				default:
					throw new Exception('Invalid ident');
			}
		}

		private function SetActive($Active) 
		{
			if($Active) {
				SetValue($this->GetIDForIdent('Active'), $Active);
				$this->ChangeLight();
				$this->SetTimerInterval('ChangeTimer', 1000);
			} else {
				SetValue($this->GetIDForIdent('Active'), $Active);
				$this->SetTimerInterval('ChangeTimer', 0);
			}
			
		}

		public function ChangeLight()
		{
			//Creating array with targetIDs
			$targetList = json_decode($this->ReadPropertyString('Targets'), true);
			$targetIDs = [];
			foreach ($targetList as $line) {
				$targetIDs[] = $line['VariableID'];
			}

			//Creating array witch colorValues
			$colorList = json_decode($this->ReadPropertyString('SwitchColors'), true);
			$colorValues = [];
			foreach ($colorList as $line) {
				$colorValues[] = $line['Color'];
			}
		
			$colorIndex = mt_rand(0, count($colorValues) - 1);

			SetValue($this->GetIDForIdent('ColorDisplay'), $colorValues[$colorIndex]);
		
			$this->SendDebug('Color Change', 'Success', 0);
		}

	}


