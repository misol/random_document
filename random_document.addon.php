<?php
/**
 * @file random_document.addon.php
 * @brief 랜덤 게시글을 추출해 게시판 상단에 표시 합니다.
 * @author MinSoo Kim (misol.kr@gmail.com)
 **/
if(!defined('__XE__')) exit();
if($called_position == 'after_module_proc' && Context::get('document_list') && class_exists('documentItem') && $addon_info->list_count){
	// 접속 모듈 정보 취득
	$module_info = Context::get('module_info');

	// 오브젝트 초기화
	$obj = new stdClass();
	$obj->module_srl = $module_info->module_srl;

	$obj->list_count = $addon_info->list_count;
	$obj->is_notice = 'N';

	// AND OR 조건 설정
	if($addon_info->readed_count && $addon_info->readed_count_op == "AND") $obj->readed_count = $addon_info->readed_count;
	if($addon_info->readed_count && $addon_info->readed_count_op == "OR") $obj->readed_count_or = $addon_info->readed_count;
	if($addon_info->voted_count && $addon_info->voted_count_op == "AND") $obj->voted_count = $addon_info->voted_count;
	if($addon_info->voted_count && $addon_info->voted_count_op == "OR") $obj->voted_count_or = $addon_info->voted_count;
	if($addon_info->comment_count && $addon_info->comment_count_op == "AND") $obj->comment_count = $addon_info->comment_count;
	if($addon_info->comment_count && $addon_info->comment_count_op == "OR") $obj->comment_count_or = $addon_info->comment_count;

	// 캐시 타임 설정
	if(!$addon_info->cache_time) $cache_time = 0;
	else $cache_time = 60 * $addon_info->cache_time;

	// 추출 시간 범위 설정
	if($addon_info->chk_time){
		$chk_time = time()-(60*60*$addon_info->chk_time);
		$chk_time = date('YmdHis',$chk_time);
		$obj->regdate = $chk_time;
	}

	$obj->sort_index = 'RAND()';/* sort in Random order */

	// 캐시 관련 ( 캐시 설정이 되어있는 상태에서 캐시가 만료 되지 않았을 경우에는 캐시에서 데이터를 취득 )
	$oCacheHandler = CacheHandler::getInstance();

	// 사이트가 캐시를 지원하고 해당 게시판의 화제의 글  캐시가 존재 할 때
	if($cache_time && $oCacheHandler->isSupport() && $oCacheHandler->isValid("random_document_$module_info->module_srl",$cache_time))
	{
		// 캐시를 가져와서 변수에 입력
		$cache = $oCacheHandler->get("random_document_$module_info->module_srl",$cache_time);
		$error = $cache->error;
		$message = $cache->message;
		$httpStatusCode = $cache->httpStatusCode;
		$random_document = $cache->data;
	}
	// 캐시가 만료 되거나 캐시 시간이 설정 되어 있지 않는 경우 DB를 통해 데이터 취득
	else
	{
		// 쿼리로 게시물 데이터 가져오기
		$output = executeQueryArray('addons.random_document.getRandomDocuments', $obj);

		// 결과 값이 있을 때 해당 게시물을 공지사항으로 강제 변경
		// 캐시를 지원하고 캐시 타임이 설정 되어 있을 경우 게시물 데이터를 캐시로 만들기
		if($output->toBool() && $output->data) {
			foreach($output->data as $key => $val) {
				$output->data[$key]->is_notice = 'Y';
			}
			if($oCacheHandler->isSupport() && $cache_time != 0) $oCacheHandler->put("random_document_$module_info->module_srl",$output,$cache_time);
		}

		// 각 변수에 데이터 입력
		$error = $output->error;
		$message = $output->message;
		$httpStatusCode = $output->httpStatusCode;
		$random_document = $output->data;
	}

	// 게시판의 공지 리스트를 가져와서 화제의 게시글 덧붙이기
	$notice_list = Context::get('notice_list');
	foreach($random_document as $key => $val){
		$notice_list[$val->document_srl] = new documentItem();
		$notice_list[$val->document_srl]->document_srl = $val->document_srl;
		$notice_list[$val->document_srl]->lang_code = $val->lang_code;
		$notice_list[$val->document_srl]->columnList = Array();
		$notice_list[$val->document_srl]->allowscriptaccessList = Array();
		$notice_list[$val->document_srl]->allowscriptaccessKey = 0;
		$notice_list[$val->document_srl]->uploadedFiles = Array();
		$notice_list[$val->document_srl]->error = $error;
		$notice_list[$val->document_srl]->message = $message;
		$val->random_document = 'Y';
		$notice_list[$val->document_srl]->variables = (array) $val;

		$notice_list[$val->document_srl]->httpStatusCode = $httpStatusCode;
	}

	// 공지 리스트 재 설정
	Context::set('notice_list',$notice_list);
}