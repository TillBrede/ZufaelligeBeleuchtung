<?php

declare(strict_types=1);
class ZufaelligeBeleuchtung extends IPSModule
{
    public function Create()
    {
        //Never delete this line!
        parent::Create();

        //Properties
        $this->RegisterPropertyString('Targets', '[]');
        $this->RegisterPropertyString('SwitchColors', '[{"Color":242944}, {"Color":16711680}, {"Color":16777215}]');
        $this->RegisterPropertyInteger('Interval', 1);
        $this->RegisterPropertyBoolean('SimultaneousSwitching', false);

        //Variables
        $this->RegisterVariableBoolean('Active', $this->Translate('Active'), '~Switch');
        $this->EnableAction('Active');

        //Timer
        $this->RegisterTimer('ChangeTimer', 0, 'ZB_ChangeLight($_IPS[\'TARGET\']);');

        //Attribute
        $this->RegisterAttributeString('BaseValues', '[]');
    }

    public function Destroy()
    {
        //Never delete this line!
        parent::Destroy();
    }

    public function ApplyChanges()
    {
        //Never delete this line!
        parent::ApplyChanges();

        $this->SaveBaseValues();

        $this->SetStatus(102);
        if (GetValue($this->GetIDForIdent('Active'))) {
            $this->ChangeLight();
        }

        //Deleting references in order to re-add them
        foreach ($this->GetReferenceList() as $referenceID) {
            $this->UnregisterReference($referenceID);
        }

        //Adding references
        $targetList = json_decode($this->ReadPropertyString('Targets'), true);
        foreach ($targetList as $target) {
            $this->RegisterReference($target['VariableID']);
        }

        if (GetValue($this->GetIDForIdent('Active'))) {
            SetValue($this->GetIDForIdent('Active'), GetValue($this->GetIDForIdent('Active')));
            $this->ChangeLight();
            $this->SetTimerInterval('ChangeTimer', $this->ReadPropertyInteger('Interval') * 1000);
        } else {
            SetValue($this->GetIDForIdent('Active'), GetValue($this->GetIDForIdent('Active')));
            $this->SetTimerInterval('ChangeTimer', 0);
        }
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

    public function ChangeLight()
    {
        if ($this->GetStatus() != 102) {
            return;
        }
        //Creating array with targetIDs
        $targetList = json_decode($this->ReadPropertyString('Targets'), true);
        $targetIDs = [];
        foreach ($targetList as $target) {
            $targetIDs[] = $target['VariableID'];
        }

        //Creating array witch colorValues
        $colorList = json_decode($this->ReadPropertyString('SwitchColors'), true);
        $colorValueList = [];
        foreach ($colorList as $target) {
            $colorValueList[] = $target['Color'];
        }

        if ($this->ReadPropertyBoolean('SimultaneousSwitching')) {
            if (count($targetIDs) == 0) {
                return;
            }
            $colorValues = $this->GetNewColor(GetValue($targetIDs[0]), $colorValueList);
            if (empty($colorValues)) {
                $this->SetStatus(200);
                return;
            }
            $colorIndex = random_int(0, count($colorValues) - 1);
            foreach ($targetIDs as $targetID) {
                RequestAction($targetID, $colorValues[$colorIndex]);
            }
        } else {
            foreach ($targetIDs as $targetID) {
                $colorValues = $this->GetNewColor(GetValue($targetID), $colorValueList);
                if (empty($colorValues)) {
                    $this->SetStatus(200);
                    return;
                }
                $colorIndex = random_int(0, count($colorValues) - 1);
                RequestAction($targetID, $colorValues[$colorIndex]);
            }
        }
    }

    private function SetActive($Active)
    {
        if ($Active) {
            SetValue($this->GetIDForIdent('Active'), $Active);
            $this->SaveBaseValues();
            $this->ChangeLight();
            $this->SetTimerInterval('ChangeTimer', $this->ReadPropertyInteger('Interval') * 1000);
        } else {
            SetValue($this->GetIDForIdent('Active'), $Active);
            $this->ResetValues();
            $this->SetTimerInterval('ChangeTimer', 0);
        }
    }

    private function SaveBaseValues()
    {
        //Creating array with targetIDs
        $targetList = json_decode($this->ReadPropertyString('Targets'), true);
        $savedBaseValues = json_decode($this->ReadAttributeString('BaseValues'), true);
        $baseValues = [];
        foreach ($targetList as $target) {
            if (array_key_exists($target['VariableID'], $savedBaseValues)) {
                $baseValues[$target['VariableID']] = $savedBaseValues[$target['VariableID']];
            } else {
                $baseValues[$target['VariableID']] = GetValue($target['VariableID']);
            }
        }
        $this->WriteAttributeString('BaseValues', json_encode($baseValues));
    }

    private function ResetValues()
    {
        //Creating array with targetIDs
        $targetList = json_decode($this->ReadPropertyString('Targets'), true);
        $baseValues = json_decode($this->ReadAttributeString('BaseValues'), true);
        foreach ($targetList as $target) {
            RequestAction($target['VariableID'], $baseValues[$target['VariableID']]);
        }
    }

    private function GetNewColor($currentValue, $colorList)
    {
        $newColors = [];
        foreach ($colorList as $color) {
            if ($currentValue != $color) {
                $newColors[] = $color;
            }
        }
        return $newColors;
    }
}
