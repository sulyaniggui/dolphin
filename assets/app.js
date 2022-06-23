/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';
import 'bootstrap'
// start the Stimulus application
import './bootstrap';

const stateVoteTicket = document.querySelector('.ticket_reactionState');
const stateReportTicket = document.querySelector('.ticket_reportState');
const ticketUpvote = document.querySelector('.ticketUpvote')
const ticketDownvote = document.querySelector('.ticketDownvote')
const ticketReportButton = document.querySelector('.ticket_reportButton')

if (stateVoteTicket.dataset.statevote === 'upvote'){
    ticketUpvote.className = 'ticketUpvote btn btn-success'
}
if (stateVoteTicket.dataset.statevote === 'downvote') {
    ticketDownvote.className = 'ticketDownvote btn btn-danger'
}

const stateVoteComments = document.querySelectorAll('.comment_reactionState');
const stateReportComments = document.querySelectorAll('.comment_reportState');
const commentsUpvote = document.querySelectorAll('.commentUpvote')
const commentsDownvote = document.querySelectorAll('.commentDownvote')
const commentsReportButton = document.querySelectorAll('.comment_reportButton')

stateVoteComments.forEach((stateVoteComment, index)=>{
    if (stateVoteComment.dataset.statevotecomment === 'upvote'){
        commentsUpvote[index].className = 'commentUpvote btn btn-success'
    }
    if (stateVoteComment.dataset.statevotecomment === 'downvote') {
        commentsDownvote[index].className = 'commentDownvote btn btn-danger'
    }
})


stateReportComments.forEach((stateReportComment, index)=>{
    if (stateReportComment.dataset.statereportcomment === 'reportOn'){
        commentsReportButton[index].setAttribute('hidden', true)
        commentsReportButton[index].setAttribute('disabled', true)
    } else {
        commentsReportButton[index].className = 'comment_reportButton btn btn-outline-dark'
    }
})


if (stateReportTicket.dataset.statereport === 'reportOn'){
    ticketReportButton.setAttribute('hidden', true)
    ticketReportButton.setAttribute('disabled', true)
} else {
    ticketReportButton.className = 'ticket_reportButton btn btn-outline-dark'
}
