<?php

namespace TheFox\MachO;

class EhFrameHdrCfiRecord
{
    /**
     * @var EhFrameHdrCieRecord
     */
    private $cie;

    /**
     * @var EhFrameHdrFdeRecord
     */
    private $fde;

    /**
     * @param EhFrameHdrCieRecord $cie
     */
    public function setCie(EhFrameHdrCieRecord $cie)
    {
        $this->cie = $cie;
    }

    /**
     * @return EhFrameHdrCieRecord
     */
    public function getCie(): EhFrameHdrCieRecord
    {
        return $this->cie;
    }

    /**
     * @param EhFrameHdrFdeRecord $fde
     */
    public function setFde(EhFrameHdrFdeRecord $fde)
    {
        $this->fde = $fde;
    }

    /**
     * @return EhFrameHdrFdeRecord
     */
    public function getFde(): EhFrameHdrFdeRecord
    {
        return $this->fde;
    }
}
