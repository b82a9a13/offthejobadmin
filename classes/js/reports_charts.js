function charts_btn_clicked(type){
    const btn = document.getElementById(`${type}_btn`);
    const div = document.getElementById(`${type}_div`);
    if(btn.innerHTML.includes('Show')){
        btn.innerHTML = btn.innerHTML.replace('Show', 'Hide');
        const errorTxt = document.getElementById('chart_error');
        errorTxt.style.display = 'none';
        const xhr = new XMLHttpRequest();
        xhr.open('POST', './classes/inc/reports_charts_render.inc.php', true);
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