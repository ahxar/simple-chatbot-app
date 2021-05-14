<template>
  <breeze-authenticated-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Messages
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="container">
              <div class="row justify-content-center">
                <div class="col-md-8">
                  <div
                    class="mb-2"
                    v-for="message in userMessages"
                    :key="message.id"
                  >
                    {{ message.user.name }}: {{ message.message }}
                  </div>

                  <div class="pt-2">
                    <breeze-input
                      type="text"
                      name="message"
                      class="form-control"
                      placeholder="Type your message here..."
                      v-model="newMessage"
                      @keyup.enter="sendMessage"
                    />

                    <breeze-button class="ml-2 py-3" @click="sendMessage">
                      Send
                    </breeze-button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </breeze-authenticated-layout>
</template>

<script>
import { ref, reactive } from "vue";
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated";
import BreezeButton from "@/Components/Button";
import BreezeInput from "@/Components/Input";

export default {
  components: {
    BreezeAuthenticatedLayout,
    BreezeButton,
    BreezeInput,
  },

  props: {
    auth: Object,
    errors: Object,
    messages: Array,
  },

  setup(props) {
    const newMessage = ref("");
    const userMessages = reactive(props.messages);

    Echo.private("chat").listen("MessageSentEvent", (e) => {
      userMessages.push({
        message: e.message.message,
        user: e.user,
      });
    });

    const addMessage = (message) => {
      userMessages.push({
        message,
        user: props.auth.user,
      });
      axios
        .post("/messages", {
          message,
        })
        .then((response) => {
          if (response.data.message) {
            userMessages.push({
              message: response.data.message.message,
              user: response.data.user,
            });
          }
        });
    };

    const sendMessage = () => {
      addMessage(newMessage.value);
      newMessage.value = "";
    };

    return { newMessage, addMessage, sendMessage, userMessages };
  },
};
</script>
