<?php

namespace WebmanTech\AmisAdmin\Model;

trait HasAmisAttributeDefine
{
    protected ?AmisAttributeDefine $amisAttributeDefine = null;

    public function amisAttributeDefine(): AmisAttributeDefine
    {
        if ($this->amisAttributeDefine === null) {
            $this->amisAttributeDefine = $this->initAmisAttributeDefine(new AmisAttributeDefine());
        }
        return $this->amisAttributeDefine;
    }

    abstract protected function initAmisAttributeDefine(AmisAttributeDefine $define): AmisAttributeDefine;
}