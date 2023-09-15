$('#pick_form')[0].addEventListener('submit', (e)=>{
    e.preventDefault();
    const errorTxt = $('#pick_error')[0];
    errorTxt.style.display = 'none';
    const value = $('#pick_select')[0];
    if(value.value == ""){
        errorTxt.innerText = 'Select a learner and course.';
        errorTxt.style.display = 'block';
    } else {
        const arrayTxt = value.value.split("-");
        window.location.href = `./admin_user.php?uid=${arrayTxt[0]}&cid=${arrayTxt[1]}`;
    }
})