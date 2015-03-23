# Random document
XE addon for Random Documents extraction.

게시판 글을 순서 없이 랜덤하게 불러옵니다. MySql 에서만 동작을 확인했습니다.

- 이 애드온은 @Canto https://github.com/canto 님의 <a href="https://www.xpressengine.com/index.php?mid=download&package_id=22753506" target="_blank">화제의 글 애드온</a>을 기반으로 제작되었습니다.  


# 추가 설정 방법

- *화제의 글* 애드온과 유사한 방식으로 수정합니다.

- 이 애드온만 사용한다면(예시)
```html
<!--@if($document->get('random_document'))-->
<span style="background-color: #ff0000;border-radius: 3px;color:#fff;padding: 2px;">랜덤 추천</span>
<!--@else-->
{$lang->notice}
<!--@end-->
```

- 화제의 글 애드온도 사용한다면?(예시)
```html
<!--@if($document->get('random_document'))-->
<span style="background-color: #ff0000;border-radius: 3px;color:#fff;padding: 2px;">랜덤 추천</span>
<!--@elseif($document->get('hot_document'))-->
<span style="background-color: #ff0000;border-radius: 3px;color:#fff;padding: 2px;">화제의 글</span>
<!--@else-->
{$lang->notice}
<!--@end-->
```
