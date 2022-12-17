const updateSize = () => {

    window.top.postMessage({
        height: document.body.scrollHeight // height in pixels
    }, '*') ;

}

updateSize() ; // set the heigth for the first time

// monitor on size changes and update the height based on those events.
// create an Observer instance to check if the screen changes
const resizeObserver = new ResizeObserver((entries) => {
    updateSize() ;
})

// start observing a DOM node
resizeObserver.observe(document.body)