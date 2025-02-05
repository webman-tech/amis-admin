<?php

namespace WebmanTech\AmisAdmin\Amis\ActionPage;

use WebmanTech\AmisAdmin\Amis\Page;

class IframeActionPage extends BaseActionPage
{
    protected string $link;

    public function __construct(string $link)
    {
        $this->link = $link;
    }

    /**
     * @inheritDoc
     */
    protected function pageSchema()
    {
        return Page::make()
            ->withBody(1, [
                'type' => 'iframe',
                'src' => urldecode($this->link),
            ]);
    }
}
