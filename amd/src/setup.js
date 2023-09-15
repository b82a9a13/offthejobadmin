$('#setup_form')[0].addEventListener('submit', (e)=>{
    e.preventDefault();
    const success = $('#setup_success')[0];
    success.style.display = 'none';
    const error = $('#setup_error')[0];
    error.style.display = 'none';
    error.innerText = '';
    const totalmonths = $('#totalmonths')[0].value;
    const totalhours = $('#totalhours')[0].value;
    const eors = $('#eors')[0].value;
    const coach = $('#coach')[0].value;
    const morm = $('#morm')[0].value;
    const startdate = $('#startdate')[0].value;
    const hpw = $('#hpw')[0].value;
    const alw = $('#alw')[0].value;
    const trainplan = $('#trainplan')[0].value;
    const option = $('#option')[0].value;
    const params = `totalmonths=${totalmonths}&totalhours=${totalhours}&eors=${eors}&coach=${coach}&morm=${morm}&startdate=${startdate}&hpw=${hpw}&alw=${alw}&trainplan=${trainplan}&option=${option}`;
    const xhr = new XMLHttpRequest();
    xhr.open('POST', './classes/inc/setup.inc.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function(){
        if(this.status == 200){
            const text = JSON.parse(this.responseText);
            if(text['error']){
                if(text['error'].length > 0){
                    let i = 0;
                    for(const errors of text['error']){
                        if(i == 0){
                            error.innerText += errors;
                            i++;
                        } else {
                            error.innerText += ', '+errors;
                        }
                    }
                    error.innerText += '.';
                } else {
                    error.innerText = text['error'][0];
                }
                error.style.display = 'block';
            } else if(text['return']){
                success.style.display = 'block';
            } else {
                error.innerText = 'Update error.';
                error.style.display = 'block';
            }
        } else {
            error.innerText = 'Connection error.';
            error.style.display = 'block';
        }
    }
    xhr.send(params);
});
function plan_clicked(){
    const select = $('#option')[0];
    const td = $('#option_td')[0];
    const th = $('#option_th')[0];
    const int = $(`#option_${$('#trainplan')[0].value}`)[0].getAttribute('options');
    select.innerHTML = '';
    let option = document.createElement('option');
    option.value = '';
    option.text = 'Choose A Option';
    option.selected = true;
    option.disabled = true;
    option.hidden = true;
    select.appendChild(option);
    if(int > 0){
        select.required = true;
        for(let i = 1; i <= int; i++){
            option = document.createElement('option');
            option.value = i;
            option.text = 'Option '+i;
            select.appendChild(option);
        }
        th.style.display = 'block';
        td.style.display = 'block';
    } else {
        select.required = false;
        td.style.display = 'none';
        th.style.display = 'none';
    }
}