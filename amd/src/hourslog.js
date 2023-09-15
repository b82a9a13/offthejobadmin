let logType = 'none';
function reset_log_ids(){
    update_delete();
    $('#lt_success')[0].style.display = 'none';
    $('#lt_error')[0].style.display = 'none';
    const ids = document.querySelectorAll('.logs-btns');
    ids.forEach(function(item){
        item.className = 'logs-btns btn';
        item.disabled = true;
    });
    logType = 'none';
}
function update_log_ids(){
    $('#lt_success')[0].style.display = 'none';
    $('#lt_error')[0].style.display = 'none';
    const ids = document.querySelectorAll('.logs-btns');
    ids.forEach(function(item){
        item.className = 'logs-btns btn btn-primary';
        item.disabled = false;
    });
    logType = 'update';
}
function delete_log_ids(){
    $('#lt_success')[0].style.display = 'none';
    $('#lt_error')[0].style.display = 'none';
    const ids = document.querySelectorAll('.logs-btns');
    ids.forEach(function(item){
        item.className = 'logs-btns btn btn-danger';
        item.disabled = false;
    });
    logType = 'delete';
}
function clicked_log_id(id){
    $('#lt_success')[0].style.display = 'none';
    const errorTxt = $('#lt_error')[0];
    errorTxt.style.display = 'none';
    const xhr = new XMLHttpRequest();
    xhr.open('POST', `./classes/inc/hourslog_${logType}.inc.php`, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    if(logType == 'update'){
        xhr.onload = function(){
            if(this.status == 200){
                const text = JSON.parse(this.responseText);
                if(text['error']){
                    errorTxt.innerText = text['error'];
                    errorTxt.style.display = 'block';
                } else if(text['return']){
                    const urDiv = $('#update_record_div')[0];
                    urDiv.innerHTML = text['return'];
                    const script = document.createElement('script');
                    script.src = './amd/min/hourslog_update_data.min.js';
                    urDiv.appendChild(script);
                    urDiv.scrollIntoView();
                } else {
                    errorTxt.innerText = 'Error loading.';
                    errorTxt.style.display = 'block';
                }
            } else {
                errorTxt.innerText = 'Connection error.';
                errorTxt.style.display = 'block';
            }
        }
    } else if(logType == 'delete'){
        xhr.onload = function(){
            if(this.status == 200){
                const text = JSON.parse(this.responseText);
                if(text['error']){
                    errorTxt.innerText = text['error'];
                    errorTxt.style.display = 'block';
                } else if(text['return']){
                    update_table();
                    refresh_it();
                    refresh_bar();
                    let success = $('#lt_success')[0];
                    success.innerText = 'Deletion Success';
                    success.style.display = 'block';
                    success.scrollIntoView();
                } else {
                    errorTxt.innerText = 'Deletion error.';
                    errorTxt.style.display = 'block';
                }
            } else {
                errorTxt.innerText = 'Connection error.';
                errorTxt.style.display = 'block';
            }
        }
    }
    xhr.send(`id=${id}`);
}
function update_table(){
    const errorTxt = $('#ut_error')[0];
    errorTxt.style.display = 'none';
    const xhr = new XMLHttpRequest();
    xhr.open('POST', './classes/inc/hourslog_update_table.inc.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function(){
        if(this.status == 200){
            const text = JSON.parse(this.responseText);
            if(text['return']){
                let tbody = $('#logs_table_tbody')[0];
                tbody.innerHTML = '';
                text['return'].forEach(function(item){
                    let tr = document.createElement('tr');
                    let td = document.createElement('td');
                    let button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'logs-btns btn';
                    button.setAttribute('onclick', 'clicked_log_id('+item[1]+')');
                    button.disabled = true;
                    button.innerText = item[0];
                    td.appendChild(button);
                    tr.appendChild(td);
                    let int = 2;
                    while(int < 7){
                        td = document.createElement('td');
                        td.innerText = item[int];
                        tr.appendChild(td);
                        int++;
                    }
                    td = document.createElement('td');
                    let atag = document.createElement('a');
                    atag.href = './../../user/profile.php?id='+item[7];
                    atag.target = '_blank';
                    atag.innerText = item[8];
                    td.appendChild(atag);
                    tr.appendChild(td);
                    tbody.appendChild(tr);
                });
            } else {
                errorTxt.innerText = 'Data loading error, when updating table.';
                errorTxt.style.display = 'block';
            }
        } else {
            errorTxt.innerText = 'Connection error, when updating table.';
            errorTxt.style.display = 'block';
        }
    }
    xhr.send();
}
function refresh_it(){
    const xhr = new XMLHttpRequest();
    xhr.open('POST', './classes/inc/hourslog_refresh_it.inc.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function(){
        const text = JSON.parse(this.responseText);
        if(text['return']){
            $('#it_total_left')[0].innerText = text['return'];
        }
    }
    xhr.send();
}
refresh_bar();
function refresh_bar(){
    const xhr = new XMLHttpRequest();
    xhr.open('POST', './classes/inc/hourslog_refresh_bar.inc.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function(){
        if(this.status == 200){
            const text = JSON.parse(this.responseText);
            if(text['return']){
                const progress = text['return'][0];
                const expected = (progress >= text['return'][1]) ? 0 : text['return'][1];
                $('#otjh_prog_progress_p')[0].innerText = `: ${progress}%`;
                $('#otjh_prog_expected_p')[0].innerText = `: ${expected}%`;
                $('#otjh_prog_incomplete_p')[0].innerText = `: ${100 - progress}%`;
                const progressbar = $('#progressbar')[0];
                if(progress >= expected){
                    progressbar.style = `width: ${progress}%; height: 25px; background-color: green;`;
                    $('#expectedbar')[0].style = `width: 0%; height: 25px; background-color: orange;`
                } else {
                    progressbar.style = `width: ${progress}%; height: 25px; background-color: green;`;
                    $('#expectedbar')[0].style = `width: ${expect}%; height: 25px; background-color: orange;`
                }
            }
        }
    }
    xhr.send();
}
function update_delete(){
    $('update_record_div')[0].innerHTML = '';
}