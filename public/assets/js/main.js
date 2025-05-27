/**
* Template Name: NiceAdmin
* Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
* Updated: Apr 7 2024 with Bootstrap v5.3.3
* Author: BootstrapMade.com
* License: https://bootstrapmade.com/license/
*/

(function() {
  "use strict";

  /**
   * Easy selector helper function
   */
  const select = (el, all = false) => {
    el = el.trim()
    if (all) {
      return [...document.querySelectorAll(el)]
    } else {
      return document.querySelector(el)
    }
  }

  /**
   * Easy event listener function
   */
  const on = (type, el, listener, all = false) => {
    if (all) {
      select(el, all).forEach(e => e.addEventListener(type, listener))
    } else {
      select(el, all).addEventListener(type, listener)
    }
  }

  /**
   * Easy on scroll event listener 
   */
  const onscroll = (el, listener) => {
    el.addEventListener('scroll', listener)
  }

  /**
   * Sidebar toggle
   */
  if (select('.toggle-sidebar-btn')) {
    on('click', '.toggle-sidebar-btn', function(e) {
      select('body').classList.toggle('toggle-sidebar')
    })
  }

  /**
   * Search bar toggle
   */
  if (select('.search-bar-toggle')) {
    on('click', '.search-bar-toggle', function(e) {
      select('.search-bar').classList.toggle('search-bar-show')
    })
  }

  /**
   * Navbar links active state on scroll
   */
  let navbarlinks = select('#navbar .scrollto', true)
  const navbarlinksActive = () => {
    let position = window.scrollY + 200
    navbarlinks.forEach(navbarlink => {
      if (!navbarlink.hash) return
      let section = select(navbarlink.hash)
      if (!section) return
      if (position >= section.offsetTop && position <= (section.offsetTop + section.offsetHeight)) {
        navbarlink.classList.add('active')
      } else {
        navbarlink.classList.remove('active')
      }
    })
  }
  window.addEventListener('load', navbarlinksActive)
  onscroll(document, navbarlinksActive)

  /**
   * Toggle .header-scrolled class to #header when page is scrolled
   */
  let selectHeader = select('#header')
  if (selectHeader) {
    const headerScrolled = () => {
      if (window.scrollY > 100) {
        selectHeader.classList.add('header-scrolled')
      } else {
        selectHeader.classList.remove('header-scrolled')
      }
    }
    window.addEventListener('load', headerScrolled)
    onscroll(document, headerScrolled)
  }

  /**
   * Back to top button
   */
  let backtotop = select('.back-to-top')
  if (backtotop) {
    const toggleBacktotop = () => {
      if (window.scrollY > 100) {
        backtotop.classList.add('active')
      } else {
        backtotop.classList.remove('active')
      }
    }
    window.addEventListener('load', toggleBacktotop)
    onscroll(document, toggleBacktotop)
  }

  /**
   * Initiate tooltips
   */
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
  })

  /**
   * Initiate quill editors
   */
  if (select('.quill-editor-default')) {
    new Quill('.quill-editor-default', {
      theme: 'snow'
    });
  }

  if (select('.quill-editor-bubble')) {
    new Quill('.quill-editor-bubble', {
      theme: 'bubble'
    });
  }

  if (select('.quill-editor-full')) {
    new Quill(".quill-editor-full", {
      modules: {
        toolbar: [
          [{
            font: []
          }, {
            size: []
          }],
          ["bold", "italic", "underline", "strike"],
          [{
              color: []
            },
            {
              background: []
            }
          ],
          [{
              script: "super"
            },
            {
              script: "sub"
            }
          ],
          [{
              list: "ordered"
            },
            {
              list: "bullet"
            },
            {
              indent: "-1"
            },
            {
              indent: "+1"
            }
          ],
          ["direction", {
            align: []
          }],
          ["link", "image", "video"],
          ["clean"]
        ]
      },
      theme: "snow"
    });
  }

  /**
   * Initiate TinyMCE Editor
   */

  const useDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
  const isSmallScreen = window.matchMedia('(max-width: 1023.5px)').matches;

  // tinymce.init({
  //   selector: 'textarea.tinymce-editor',
  //   plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons accordion',
  //   editimage_cors_hosts: ['picsum.photos'],
  //   menubar: 'file edit view insert format tools table help',
  //   toolbar: "undo redo | accordion accordionremove | blocks fontfamily fontsize | bold italic underline strikethrough | align numlist bullist | link image | table media | lineheight outdent indent| forecolor backcolor removeformat | charmap emoticons | code fullscreen preview | save print | pagebreak anchor codesample | ltr rtl",
  //   autosave_ask_before_unload: true,
  //   autosave_interval: '30s',
  //   autosave_prefix: '{path}{query}-{id}-',
  //   autosave_restore_when_empty: false,
  //   autosave_retention: '2m',
  //   image_advtab: true,
  //   link_list: [{
  //       title: 'My page 1',
  //       value: 'https://www.tiny.cloud'
  //     },
  //     {
  //       title: 'My page 2',
  //       value: 'http://www.moxiecode.com'
  //     }
  //   ],
  //   image_list: [{
  //       title: 'My page 1',
  //       value: 'https://www.tiny.cloud'
  //     },
  //     {
  //       title: 'My page 2',
  //       value: 'http://www.moxiecode.com'
  //     }
  //   ],
  //   image_class_list: [{
  //       title: 'None',
  //       value: ''
  //     },
  //     {
  //       title: 'Some class',
  //       value: 'class-name'
  //     }
  //   ],
  //   importcss_append: true,
  //   file_picker_callback: (callback, value, meta) => {
  //     /* Provide file and text for the link dialog */
  //     if (meta.filetype === 'file') {
  //       callback('https://www.google.com/logos/google.jpg', {
  //         text: 'My text'
  //       });
  //     }

  //     /* Provide image and alt text for the image dialog */
  //     if (meta.filetype === 'image') {
  //       callback('https://www.google.com/logos/google.jpg', {
  //         alt: 'My alt text'
  //       });
  //     }

  //     /* Provide alternative source and posted for the media dialog */
  //     if (meta.filetype === 'media') {
  //       callback('movie.mp4', {
  //         source2: 'alt.ogg',
  //         poster: 'https://www.google.com/logos/google.jpg'
  //       });
  //     }
  //   },
  //   height: 600,
  //   image_caption: true,
  //   quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
  //   noneditable_class: 'mceNonEditable',
  //   toolbar_mode: 'sliding',
  //   contextmenu: 'link image table',
  //   skin: useDarkMode ? 'oxide-dark' : 'oxide',
  //   content_css: useDarkMode ? 'dark' : 'default',
  //   content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }'
  // });

  /**
   * Initiate Bootstrap validation check
   */
  var needsValidation = document.querySelectorAll('.needs-validation')

  Array.prototype.slice.call(needsValidation)
    .forEach(function(form) {
      form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }

        form.classList.add('was-validated')
      }, false)
    })

  /**
   * Initiate Datatables
   */
  const datatables = select('.datatable', true)
  datatables.forEach(datatable => {
    new simpleDatatables.DataTable(datatable, {
      perPageSelect: [5, 10, 15, ["All", -1]],
      columns: [{
          select: 2,
          sortSequence: ["desc", "asc"]
        },
        {
          select: 3,
          sortSequence: ["desc"]
        },
        {
          select: 4,
          cellClass: "green",
          headerClass: "red"
        }
      ]
    });
  })

  /**
   * Autoresize echart charts
   */
  const mainContainer = select('#main');
  if (mainContainer) {
    setTimeout(() => {
      new ResizeObserver(function() {
        select('.echart', true).forEach(getEchart => {
          echarts.getInstanceByDom(getEchart).resize();
        })
      }).observe(mainContainer);
    }, 200);
  }
  document.addEventListener("DOMContentLoaded", function () {
    // function handleDateInput(event, inputField, isDateTime = false) {
    //     if (!inputField.value) {
    //         inputField.value = formatDate(new Date(), isDateTime); // Default to now if empty
    //     }

    //     let currentDate = new Date(inputField.value);

    //     if (event.key === "+" || event.key === "=") {
    //         event.preventDefault();
    //         currentDate.setDate(currentDate.getDate() + 1);
    //     } else if (event.key === "-" || event.key === "_") {
    //         event.preventDefault();
    //         currentDate.setDate(currentDate.getDate() - 1);
    //     } else if (event.key.toLowerCase() === "t") {
    //         event.preventDefault();
    //         currentDate = new Date(); 
    //     }

    //     inputField.value = formatDate(currentDate, isDateTime);
    // }

    // function formatDate(date, isDateTime = false) {
    //     let year = date.getFullYear();
    //     let month = String(date.getMonth() + 1).padStart(2, '0');
    //     let day = String(date.getDate()).padStart(2, '0');

    //     if (isDateTime) {
    //         let hours = String(date.getHours()).padStart(2, '0');
    //         let minutes = String(date.getMinutes()).padStart(2, '0');
    //         return `${year}-${month}-${day}T${hours}:${minutes}`;
    //     }

    //     return `${year}-${month}-${day}`;
    // }

    // const dateInput = document.getElementById("delivery_deadline");
    // const dateTimeInput = document.getElementById("schedule");

    // if (dateInput) {
    //     dateInput.addEventListener("keydown", function (event) {
    //         handleDateInput(event, dateInput, false);
    //     });
    // }

    // if (dateTimeInput) {
    //     dateTimeInput.addEventListener("keydown", function (event) {
    //         handleDateInput(event, dateTimeInput, true);
    //     });
    // }

    
});

document.addEventListener("DOMContentLoaded", function () {
  // const input = document.getElementById("schedule");
  // const trigger = document.getElementById("calendar-trigger");
  const deinput = document.getElementById("delivery_deadline");
  const detrigger = document.getElementById("de-calendar-trigger");
    const scheduleDateInput = document.getElementById("schedule_date");
    const scheduleDateTrigger = document.getElementById("date-picker-trigger");

    const scheduleTimeInput = document.getElementById("schedule_time");
    const scheduleTimeTrigger = document.getElementById("time-picker-trigger");

  if (typeof flatpickr !== "undefined") {
    const decalendar = flatpickr(deinput, {
        dateFormat: "M. j, Y",
          allowInput: true,
          clickOpens: false,
      });

      detrigger.addEventListener('click', () => {
        decalendar.open();
        deinput.focus();
    });

    //   const calendar = flatpickr(input, {
    //     enableTime: true,
    //     dateFormat: "M. j, Y H:i",
    //     time_24hr: true,
    //     defaultHour: 9,
    //     allowInput: true ,
    //     clickOpens: false
    // });

    //   trigger.addEventListener('click', () => {
    //     calendar.open();
    //     input.focus();

    // });

  const datePicker = flatpickr(scheduleDateInput, {
            dateFormat: "M. j, Y",
            allowInput: true,
            clickOpens: false,
            defaultDate: scheduleDateInput.value || new Date()
        });

        // Initialize Time Picker
        const timePicker = flatpickr(scheduleTimeInput, {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true,
            allowInput: true,
            clickOpens: false,
            defaultDate: scheduleTimeInput.value || "09:00"
        });

        // Trigger buttons
        scheduleDateTrigger.addEventListener("click", () => {
            datePicker.open();
            scheduleDateInput.focus();

        });

        scheduleTimeTrigger.addEventListener("click", () => {
            timePicker.open();
            scheduleTimeInput.focus();

        });

      function adjustDate(inputField, increase = true) {
          let instance = inputField._flatpickr;
          if (!instance) return;

          let selectedDate = instance.selectedDates[0] || new Date();
          selectedDate.setDate(selectedDate.getDate() + (increase ? 1 : -1));

          instance.setDate(selectedDate, true);
      }

      function setToday(inputField) {
          let instance = inputField._flatpickr;
          if (!instance) return;

          let today = new Date();
          instance.setDate(today, true);
      }
   scheduleDateInput.addEventListener("keydown", function (event) {
              const key = event.key;

    if (key === "+" || (key === "=" && event.shiftKey)) {
        event.preventDefault();
        adjustDate(this, true);
    } else if (key === "-" || key === "_") {
        event.preventDefault();
        adjustDate(this, false);
    } else if (key.toLowerCase() === "t") {
        event.preventDefault();
        setToday(this);
    }
        });
scheduleTimeInput.addEventListener("keydown", function (event) {
    const key = event.key;
    const instance = this._flatpickr;

    if (!instance) return;

    let date = instance.selectedDates[0] || new Date();

    if (key === "+" || (key === "=" && event.shiftKey)) {
        event.preventDefault();
        date.setHours(date.getHours() + 1);
        instance.setDate(date, true);
    } else if (key === "-" || key === "_") {
        event.preventDefault();
        date.setHours(date.getHours() - 1);
        instance.setDate(date, true);
    } else if (key.toLowerCase() === "t") {
        event.preventDefault();
        const now = new Date(); // current time
        instance.setDate(now, true);
    }
});
      document.getElementById("delivery_deadline").addEventListener("keydown", function (event) {
          if (event.key === "+" || event.key === "=") { // Increase date
              event.preventDefault();
              adjustDate(this, true);
          } else if (event.key === "-" || event.key === "_") { // Decrease date
              event.preventDefault();
              adjustDate(this, false);
          } else if (event.key.toLowerCase() === "t") { // Set to today
              event.preventDefault();
              setToday(this);
          }
      });

      document.getElementById("schedule").addEventListener("keydown", function (event) {
          if (event.key === "+" || event.key === "=") { // Increase date
              event.preventDefault();
              adjustDate(this, true);
          } else if (event.key === "-" || event.key === "_") { // Decrease date
              event.preventDefault();
              adjustDate(this, false);
          } else if (event.key.toLowerCase() === "t") { // Set to today
              event.preventDefault();
              setToday(this);
          }
      });

  } else {
      console.error("Flatpickr failed to load.");
  }
});
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
})
})();


