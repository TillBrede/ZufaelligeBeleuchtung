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
        foreach ($targetList as $line) {
            $this->RegisterReference($line['VariableID']);
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

    public function ChangeLight()
    {
        if ($this->GetStatus() != 102) {
            return;
        }
        //Creating array with targetIDs
        $targetList = json_decode($this->ReadPropertyString('Targets'), true);
        $targetIDs = [];
        foreach ($targetList as $line) {
            $targetIDs[] = $line['VariableID'];
        }

        //Creating array witch colorValues
        $colorList = json_decode($this->ReadPropertyString('SwitchColors'), true);
        $colorValueList = [];
        foreach ($colorList as $line) {
            $colorValueList[] = $line['Color'];
        }

        if ($this->ReadPropertyBoolean('SimultaneousSwitching')) {
            foreach ($targetIDs as $targetID) {
                $colorValues = $this->GetNewColor(GetValue($targetID), $colorValueList);
                if (empty($colorValues)) {
                    $this->SetStatus(200);
                    return;
                }
                $colorIndex = random_int(0, count($colorValues) - 1);
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

    private function SaveBaseValues()
    {
        //Creating array with targetIDs
        $targetList = json_decode($this->ReadPropertyString('Targets'), true);
        $baseValues = [];
        foreach ($targetList as $line) {
            $baseValues[$line['VariableID']] = GetValue($line['VariableID']);
        }
        $this->WriteAttributeString('BaseValues', json_encode($baseValues));
    }

    private function ResetValues()
    {
        //Creating array with targetIDs
        $targetList = json_decode($this->ReadPropertyString('Targets'), true);
        $baseValues = json_decode($this->ReadAttributeString('BaseValues'), true);
        foreach ($targetList as $line) {
            RequestAction($line['VariableID'], $baseValues[$line['VariableID']]);
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

