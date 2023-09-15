function progress_select(){
    const value = $('#progress_select')[0];
    progress_request(`cid=${value.value}`);
}
function progress_all(){
    progress_request(``);
}
function progress_request(params){
    const div = $('#progress_content_div')[0];
    div.style.display = 'none';
    const errorTxt = $('#progress_error')[0];
    errorTxt.style.display = 'none';
    const xhr = new XMLHttpRequest();
    xhr.open('POST', './classes/inc/reports_progress_render.inc.php', true);
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
                if(text['script']){
                    const script = document.createElement('script');
                    script.innerHTML = text['script'];
                    div.appendChild(script);
                }
            }
        } else {
            errorTxt.innerText = 'Connection error.';
            errorTxt.style.display = 'block';
        }
    }
    xhr.send(params);
}
function create_prog_circle(id, progress, expect){
    const canvas = $(`#prog_canvas_${id}`)[0];
    const ctx = canvas.getContext('2d');
    const int = 2;
    const complete = int * (progress / 100);
    const expected = int * (expect / 100);
    ctx.lineWidth = 30;
    ctx.beginPath();
    ctx.strokeStyle = 'red';
    ctx.arc(60, 60, 30, complete*Math.PI, int*Math.PI);
    ctx.stroke();
    ctx.beginPath();
    ctx.strokeStyle = 'orange';
    ctx.arc(60, 60, 30, 0, expected*Math.PI);
    ctx.stroke();
    ctx.beginPath();
    ctx.strokeStyle = 'green';
    ctx.arc(60, 60, 30, 0, complete*Math.PI);
    ctx.stroke();
}