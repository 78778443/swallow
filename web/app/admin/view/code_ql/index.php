{include file='public/head' /}
<style>
    a {
        text-decoration: none;
    }

    li {
        list-style: none;
    }
</style>
<div class="row">
    <div class="col-1 ">
        {include file='Common/nav' /}
    </div>
    <div class="col-11 tuchu" style="border-radius: 5px;">
        <div class="row">
            <div class="col-12">

                <div class="btn-group" role="group" aria-label="Basic outlined example">
                    <a type="button"
                       class="btn btn-outline-primary  <?php echo ($status == 'all') ? 'active' : '' ?> "
                       href="{:URL('index')}">全部</a>
                    <a type="button"
                       class="btn btn-outline-primary <?php echo ($status == 'high') ? 'active' : '' ?>"
                       href="{:URL('index',['status'=>'high'])}">高危</a>
                    <a type="button"
                       class="btn btn-outline-primary <?php echo ($status == 'medium') ? 'active' : '' ?>"
                       href="{:URL('index',['status'=>'medium'])}">中危</a>
                    <a type="button"
                       class="btn btn-outline-primary <?php echo ($status == 'low') ? 'active' : '' ?>"
                       href="{:URL('index',['status'=>'low'])}">低危</a>
                </div>
                
                <!-- 漏洞类型筛选 -->
                <div class="btn-group" role="group" aria-label="Vulnerability type filter" style="margin-left: 20px;">
                    <select class="form-select" id="ruleTypeSelect">
                        <option value="">所有漏洞类型</option>
                        <?php foreach ($rule_types as $type) { ?>
                            <option value="<?php echo $type['ruleId']; ?>" <?php echo isset($param['rule_id']) && $param['rule_id'] == $type['ruleId'] ? 'selected' : ''; ?>><?php echo $type['name'] ?: $type['ruleId']; ?></option>
                        <?php } ?>
                    </select>
                </div>

            </div>
        </div>
        
        <script>
        function updateFilters() {
            let ruleId = document.getElementById('ruleTypeSelect').value;
            let problemSeverity = document.getElementById('problemSeveritySelect').value;
            let ruleKind = document.getElementById('ruleKindSelect').value;
            let securitySeverity = document.getElementById('securitySeveritySelect').value;
            let status = '<?php echo isset($param['status']) ? $param['status'] : ''; ?>';
            
            let url = '{:URL('index')}';
            let params = [];
            
            if (status !== 'all') {
                params.push('status=' + status);
            }
            if (ruleId) {
                params.push('rule_id=' + ruleId);
            }
            if (problemSeverity) {
                params.push('problem_severity=' + problemSeverity);
            }
            if (ruleKind) {
                params.push('rule_kind=' + ruleKind);
            }
            if (securitySeverity) {
                params.push('security_severity=' + securitySeverity);
            }
            
            if (params.length > 0) {
                url += '?' + params.join('&');
            }
            
            window.location.href = url;
        }
        
        // Add event listeners to all select elements
        document.getElementById('ruleTypeSelect').addEventListener('change', updateFilters);
        document.getElementById('problemSeveritySelect').addEventListener('change', updateFilters);
        document.getElementById('ruleKindSelect').addEventListener('change', updateFilters);
        document.getElementById('securitySeveritySelect').addEventListener('change', updateFilters);
        </script>
        <div class="row">
            <div class="col-12">
                <div style="margin-top:20px;">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th style="color:#aaa;">id</th>
                            <th style="color:#aaa;">ruleId</th>
                            <th style="color:#aaa;">发现时间</th>
                            <th style="color:#aaa;">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($bugList['list'] as $item) { ?>
                            <tr>
                                <td>{$item['id']}</td>
                                <td>{$item['ruleId']}</td>

                                <td>{$item['create_time']}</td>
                                <td><a class="btn btn-sm btn-light" href="{:URL('detail',['id'=>$item['id']])}"
                                       aria-disabled="true">查看详情</a></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                {include file='public/fenye' /}
            </div>
        </div>
    </div>
</div>