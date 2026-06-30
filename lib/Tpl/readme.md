{* exampaper_paper.tpl *}
{extends file="layout.tpl"}

{block name="title"}{$title}{/block}

{block name="content"}
<h1>{$title}</h1>
<p>欢迎，{$user.username}</p>

{if $questions}
{foreach $questions as $q}
<div class="question">
<p>{$q.question}</p>
{foreach $q.options as $opt}
<label>
<input type="radio" name="q{$q.id}" value="{$opt.id}">
{$opt.content}
</label>
{/foreach}
</div>
{/foreach}
{else}
<p>暂无题目</p>
{/if}

{* 带修饰符 *}
<p>时间：{$user.create_time|date_format:"Y-m-d H:i"}</p>
<p>名称：{$user.username|upper}</p>

{* 不转义输出 *}
<p>{$content nofilter}</p>

{* 注释不会输出 *}
{* 这是注释 *}

{* 包含子模板 *}
{include file="header.tpl"}
{/block}