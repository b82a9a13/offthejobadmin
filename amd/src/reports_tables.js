function table_btn_clicked(type){
    const btn = $(`#${type}_btn`)[0];
    const div = $(`#table_div`)[0];
    if(btn.innerHTML.includes('Show')){
        ['ac', 'lwis', 'lwcs', 'lbt', 'ldms', 'lwap'].forEach((item)=>{
            $(`#${item}_btn`)[0].innerHTML = $(`#${item}_btn`)[0].innerHTML.replace('Hide', 'Show');
        })
        btn.innerHTML = btn.innerHTML.replace('Show', 'Hide');
        const errorTxt = $('#table_error')[0];
        errorTxt.style.display = 'none';
        const xhr = new XMLHttpRequest();
        xhr.open('POST', './classes/inc/reports_tables_render.inc.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function(){
            if(this.status == 200){
                const text = JSON.parse(this.responseText);
                if(text['error']){
                    errorTxt.innerText = text['error'];
                    errorTxt.style.display = 'block';
                } else if(text['return']){
                    div.innerHTML = text['return'];
                    div.style.display = 'block';
                }
            } else {
                errorTxt.innerText = 'Connection error.';
                errorTxt.style.display = 'block';
            }
        }
        xhr.send(`type=${type}`);
    } else if(btn.innerHTML.includes('Hide')){
        btn.innerHTML = btn.innerHTML.replace('Hide', 'Show');
        div.innerHTML = '';
        div.style.display = 'none';
    }
}