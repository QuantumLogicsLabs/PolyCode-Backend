const GROQ_API_URL = "https://api.groq.com/openai/v1/chat/completions";

function getGroqConfig() {
  const apiKey = process.env.GROQ_API_KEY?.trim();
  if (!apiKey) {
    const err = new Error(
      "GROQ_API_KEY is not configured on the server. Add it to backend/.env and restart npm run dev.",
    );
    err.statusCode = 503;
    err.code = "GROQ_NOT_CONFIGURED";
    throw err;
  }

  return {
    apiKey,
    primaryModel: process.env.GROQ_MODEL?.trim() || "openai/gpt-oss-120b",
    fallbackModel:
      process.env.GROQ_FALLBACK_MODEL?.trim() || "openai/gpt-oss-20b",
  };
}

async function callGroq(messages, model) {
  const { apiKey } = getGroqConfig();

  const response = await fetch(GROQ_API_URL, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Authorization: `Bearer ${apiKey}`,
    },
    body: JSON.stringify({
      model,
      messages,
      temperature: 0.3,
    }),
  });

  const data = await response.json().catch(() => ({}));

  if (!response.ok) {
    const detail =
      data?.error?.message ||
      data?.message ||
      `Groq API request failed (${response.status})`;
    const err = new Error(detail);
    err.statusCode = response.status;
    throw err;
  }

  const content = data?.choices?.[0]?.message?.content;
  if (!content || !String(content).trim()) {
    throw new Error("Groq returned an empty response.");
  }

  return String(content).trim();
}

/**
 * @param {Array<{role: string, content: string}>} messages
 */
async function generateAssistantReply(messages) {
  const { primaryModel, fallbackModel } = getGroqConfig();

  try {
    return await callGroq(messages, primaryModel);
  } catch (primaryError) {
    if (primaryModel === fallbackModel) {
      throw primaryError;
    }
    return callGroq(messages, fallbackModel);
  }
}

module.exports = { generateAssistantReply };
