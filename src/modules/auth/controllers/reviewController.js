const reviewService = require("../services/reviewService");

function statusFromError(error) {
  return error.statusCode || 400;
}

async function listDue(req, res) {
  try {
    const items = await reviewService.listDue(req.userId, {
      courseId: req.query.courseId,
    });
    res.json({ items, intervalDays: reviewService.REVIEW_INTERVAL_DAYS });
  } catch (error) {
    res.status(statusFromError(error)).json({ error: error.message });
  }
}

async function countDue(req, res) {
  try {
    const count = await reviewService.countDue(req.userId, {
      courseId: req.query.courseId,
    });
    res.json({ count });
  } catch (error) {
    res.status(statusFromError(error)).json({ error: error.message });
  }
}

async function answer(req, res) {
  try {
    const result = await reviewService.answerReview(
      req.userId,
      req.params.itemId,
      req.body?.correct,
    );
    res.json(result);
  } catch (error) {
    res.status(statusFromError(error)).json({ error: error.message });
  }
}

async function dismiss(req, res) {
  try {
    const result = await reviewService.dismissReview(req.userId, req.params.itemId);
    res.json(result);
  } catch (error) {
    res.status(statusFromError(error)).json({ error: error.message });
  }
}

module.exports = {
  listDue,
  countDue,
  answer,
  dismiss,
};
