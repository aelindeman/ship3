<?php

?>
@if ($count > 0)
<var data-key="Aptitude.count">{{ $count }}</var> @choice('Aptitude::component.packages', $count)
@else
@lang('Aptitude::component.no-packages')
@endif
